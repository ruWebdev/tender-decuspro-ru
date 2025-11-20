<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->foreignUuid('parent_tender_id')
                ->nullable()
                ->after('customer_id')
                ->constrained('tenders')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->unsignedInteger('round_number')
                ->default(1)
                ->after('winner_proposal_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropForeign(['parent_tender_id']);
            $table->dropColumn(['parent_tender_id', 'round_number']);
        });
    }
};
