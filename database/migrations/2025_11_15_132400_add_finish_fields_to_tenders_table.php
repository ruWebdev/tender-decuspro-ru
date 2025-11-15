<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            // Поле winner_proposal_id уже существует в базовой миграции создания таблицы tenders.
            // Здесь добавляем только поля завершения тендера.
            $table->timestamp('finished_at')->nullable()->after('valid_until');
            $table->boolean('is_finished')->default(false)->after('finished_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn(['finished_at', 'is_finished']);
        });
    }
};
