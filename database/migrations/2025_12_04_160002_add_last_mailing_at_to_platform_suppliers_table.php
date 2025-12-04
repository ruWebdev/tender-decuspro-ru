<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_suppliers', function (Blueprint $table) {
            $table->timestamp('last_mailing_at')->nullable()->after('invitation_sent');
        });
    }

    public function down(): void
    {
        Schema::table('platform_suppliers', function (Blueprint $table) {
            $table->dropColumn('last_mailing_at');
        });
    }
};
