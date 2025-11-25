<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_chats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tender_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('supplier_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('translate_to_ru')->default(false);
            $table->timestamps();

            $table->unique(['tender_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_chats');
    }
};
