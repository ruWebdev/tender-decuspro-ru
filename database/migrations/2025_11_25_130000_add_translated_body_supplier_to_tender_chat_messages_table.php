<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tender_chat_messages', function (Blueprint $table) {
            $table->text('translated_body_supplier')->nullable()->after('translated_body_ru');
        });
    }

    public function down(): void
    {
        Schema::table('tender_chat_messages', function (Blueprint $table) {
            $table->dropColumn('translated_body_supplier');
        });
    }
};
