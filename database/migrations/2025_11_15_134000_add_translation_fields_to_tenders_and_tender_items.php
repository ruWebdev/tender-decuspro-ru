<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_cn')->nullable()->after('description_en');
        });

        Schema::table('tender_items', function (Blueprint $table): void {
            $table->text('name_en')->nullable()->after('title');
            $table->text('name_cn')->nullable()->after('name_en');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropColumn(['description_en', 'description_cn']);
        });

        Schema::table('tender_items', function (Blueprint $table): void {
            $table->dropColumn(['name_en', 'name_cn']);
        });
    }
};
