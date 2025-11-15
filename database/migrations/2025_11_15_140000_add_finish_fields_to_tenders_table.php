<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->boolean('is_finished')->default(false)->after('status');
            $table->dateTime('finished_at')->nullable()->after('is_finished');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropColumn(['is_finished', 'finished_at']);
        });
    }
};
