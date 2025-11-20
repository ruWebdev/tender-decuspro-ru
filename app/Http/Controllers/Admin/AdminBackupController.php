<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SystemLogService;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\Process\Process;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBackupController extends Controller
{
    public function index(): Response
    {
        $files = collect(Storage::files('backups'))
            ->filter(fn(string $path): bool => str_ends_with($path, '.sql'))
            ->map(function (string $path): array {
                $fullPath = Storage::path($path);
                $name = basename($path);

                return [
                    'name' => $name,
                    'size' => filesize($fullPath) ?: 0,
                    'size_human' => $this->formatSize(filesize($fullPath) ?: 0),
                    'created_at' => date('c', filemtime($fullPath) ?: time()),
                ];
            })
            ->sortByDesc('created_at')
            ->values()
            ->all();

        return Inertia::render('Admin/Backup/Index', [
            'backups' => $files,
        ]);
    }

    public function run(Request $request, SystemLogService $log): RedirectResponse
    {
        $connection = config('database.default');
        $config = config("database.connections.$connection");

        if (! $config || ($config['driver'] ?? null) !== 'mysql') {
            return back()->withErrors([
                'backup' => 'Создание резервных копий через интерфейс поддерживается только для MySQL.',
            ]);
        }

        $directory = storage_path('app/backups');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = 'backup_' . ($config['database'] ?? 'database') . '_' . now()->format('Ymd_His') . '.sql';
        $fullPath = $directory . DIRECTORY_SEPARATOR . $filename;

        $user = escapeshellarg($config['username'] ?? '');
        $password = escapeshellarg($config['password'] ?? '');
        $host = escapeshellarg($config['host'] ?? '127.0.0.1');
        $port = escapeshellarg((string) ($config['port'] ?? 3306));
        $database = escapeshellarg($config['database'] ?? '');

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
            $user,
            $password,
            $host,
            $port,
            $database,
            escapeshellarg($fullPath),
        );

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(300);
        $process->run();

        if (! $process->isSuccessful() || ! file_exists($fullPath)) {
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            $log->error('backup_failed', 'Не удалось создать резервную копию через веб-интерфейс', [
                'database' => $config['database'] ?? null,
            ]);

            return back()->withErrors([
                'backup' => 'Не удалось создать резервную копию. Проверьте наличие утилиты mysqldump на сервере.',
            ]);
        }

        $log->business('backup_created', 'Создана резервная копия базы данных', [
            'database' => $config['database'] ?? null,
            'file' => $filename,
        ]);

        return back()->with('success', 'Резервная копия успешно создана');
    }

    public function download(string $file): RedirectResponse|StreamedResponse
    {
        $name = basename($file);
        $path = 'backups/' . $name;

        if (! Storage::exists($path)) {
            return back()->withErrors([
                'backup' => 'Файл резервной копии не найден.',
            ]);
        }

        return Storage::download($path);
    }

    public function destroy(string $file, SystemLogService $log): RedirectResponse
    {
        $name = basename($file);
        $path = 'backups/' . $name;

        if (Storage::exists($path)) {
            Storage::delete($path);

            $log->warning('backup_deleted', 'Резервная копия удалена', [
                'file' => $name,
            ]);
        }

        return back()->with('success', 'Резервная копия удалена');
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $i = 0;
        $size = $bytes / 1024;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return number_format($size, 2, '.', ' ') . ' ' . $units[$i];
    }
}
