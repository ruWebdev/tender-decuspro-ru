<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('company_name');
            $table->json('contact_data')->nullable();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('supplier_profile_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('path');
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->timestamps();
        });

        Schema::create('tenders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->index()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('hidden_comment')->nullable();
            $table->dateTime('valid_until');
            $table->string('status')->default('open');
            $table->uuid('winner_proposal_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('tender_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tender_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->decimal('quantity', 12, 2);
            $table->string('unit')->nullable();
            $table->json('meta')->nullable();
            $table->integer('position_index')->default(0);
            $table->timestamps();
        });

        Schema::create('proposals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tender_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('user_id')->index()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['tender_id', 'user_id']);
        });

        Schema::create('proposal_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('proposal_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('tender_item_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('locale');
            $table->string('source_type');
            $table->unsignedBigInteger('source_id');
            $table->string('field');
            $table->text('text');
            $table->string('provider')->nullable();
            $table->timestamps();
            $table->index(['locale', 'source_type', 'source_id']);
        });

        Schema::table('tenders', function (Blueprint $table) {
            $table->foreign('winner_proposal_id')
                ->references('id')->on('proposals')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropForeign(['winner_proposal_id']);
        });

        Schema::dropIfExists('translations');
        Schema::dropIfExists('proposal_items');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('tender_items');
        Schema::dropIfExists('tenders');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('supplier_profiles');
    }
};
