<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_suppliers')) {
            return;
        }

        Schema::table('platform_suppliers', function (Blueprint $table) {
            $table->string('language', 5)->nullable()->after('comment');
            $table->boolean('invitation_sent')->default(false)->after('language');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('platform_suppliers')) {
            return;
        }

        Schema::table('platform_suppliers', function (Blueprint $table) {
            $table->dropColumn(['language', 'invitation_sent']);
        });
    }
};
