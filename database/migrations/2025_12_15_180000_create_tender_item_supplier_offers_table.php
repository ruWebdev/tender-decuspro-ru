<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tender_item_supplier_offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tender_item_id')->index()->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->unsignedBigInteger('external_item_id')->nullable()->index();
            $table->unsignedBigInteger('external_parent_item_id')->nullable()->index();

            $table->string('supplier_source')->nullable();
            $table->string('supplier_type')->nullable();
            $table->string('supplier_external_id')->nullable();
            $table->string('supplier_name')->nullable();

            $table->string('availability')->nullable();

            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('price_sale', 12, 2)->nullable();

            $table->string('currency', 8)->default('RUB');
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['tender_item_id', 'supplier_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_item_supplier_offers');
    }
};
