<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailing_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mailing_id')->constrained('mailings')->cascadeOnDelete();
            $table->foreignUuid('platform_supplier_id')->constrained('platform_suppliers')->cascadeOnDelete();
            $table->string('email');
            $table->enum('status', ['sent', 'failed'])->default('sent');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['mailing_id', 'platform_supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailing_logs');
    }
};
