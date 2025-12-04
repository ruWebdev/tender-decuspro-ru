<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('emails_limit')->default(50);
            $table->foreignUuid('notification_template_id')->constrained('notification_templates')->cascadeOnDelete();
            $table->json('tender_ids')->nullable();
            $table->text('company_filter')->nullable();
            $table->enum('status', ['draft', 'running', 'paused', 'completed'])->default('draft');
            $table->integer('sent_count')->default(0);
            $table->integer('total_recipients')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailings');
    }
};
