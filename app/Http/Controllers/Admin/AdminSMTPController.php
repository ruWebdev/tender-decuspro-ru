<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class AdminSMTPController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/SMTP/Index', [
            'settings' => [
                'mail_host' => Setting::get('mail_host') ?? config('mail.mailers.smtp.host'),
                'mail_port' => Setting::get('mail_port') ?? config('mail.mailers.smtp.port'),
                'mail_username' => Setting::get('mail_username') ?? config('mail.mailers.smtp.username'),
                'mail_password' => Setting::get('mail_password') ?? config('mail.mailers.smtp.password'),
                'mail_encryption' => Setting::get('mail_encryption') ?? config('mail.mailers.smtp.encryption'),
                'mail_from_address' => Setting::get('mail_from_address') ?? config('mail.from.address'),
                'mail_from_name' => Setting::get('mail_from_name') ?? config('mail.from.name'),
            ],
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl,null',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        Setting::set('mail_host', $validated['mail_host']);
        Setting::set('mail_port', (string) $validated['mail_port']);
        Setting::set('mail_username', $validated['mail_username'] ?? null);
        Setting::set('mail_password', $validated['mail_password'] ?? null);
        Setting::set('mail_encryption', $validated['mail_encryption'] === 'null' ? null : ($validated['mail_encryption'] ?? null));
        Setting::set('mail_from_address', $validated['mail_from_address']);
        Setting::set('mail_from_name', $validated['mail_from_name']);

        return back()->with('success', 'Настройки SMTP сохранены');
    }

    /**
     * Отправка тестового письма для проверки SMTP-настроек
     */
    public function test(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $to = $data['email'];

        try {
            Mail::raw(__('admin.smtp.test.body'), function ($message) use ($to): void {
                $message->to($to)
                    ->subject(__('admin.smtp.test.subject'));
            });

            return back()->with('success', __('admin.smtp.test.success'));
        } catch (\Throwable $e) {
            Log::error('SMTP test email failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('admin.smtp.test.error'));
        }
    }
}
