<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            // Убедимся, что базовые роли существуют до назначения
            foreach (['admin', 'moderator', 'customer', 'supplier'] as $roleName) {
                Role::firstOrCreate([
                    'name' => $roleName,
                    'guard_name' => 'web',
                ]);
            }

            User::query()->chunkById(100, function ($users): void {
                foreach ($users as $user) {
                    $role = $user->role ?: 'supplier';

                    if ($role === 'user') {
                        $role = 'supplier';
                    }

                    if (! in_array($role, ['admin', 'moderator', 'customer', 'supplier'], true)) {
                        $role = 'supplier';
                    }

                    if (! $user->hasRole($role)) {
                        $user->assignRole($role);
                    }
                }
            });
        });
    }

    public function down(): void
    {
        // Обратная миграция намеренно не реализована,
        // так как восстановить исходное строковое поле role надёжно невозможно.
    }
};
