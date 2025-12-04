<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_suppliers', function (Blueprint $table) {
            $table->string('title')->nullable()->after('name');
            $table->text('all_emails')->nullable()->after('email');
            $table->text('all_phones')->nullable()->after('phone');
            $table->string('location')->nullable()->after('website');
            $table->string('contact_person')->nullable()->after('location');
            $table->integer('established_year')->nullable()->after('contact_person');
            $table->integer('employee_count')->nullable()->after('established_year');
            $table->text('description')->nullable()->after('comment');
            $table->text('main_products')->nullable()->after('description');
            $table->text('export_markets')->nullable()->after('main_products');
            $table->text('certifications')->nullable()->after('export_markets');
            $table->string('keyword')->nullable()->after('certifications');
            $table->string('source_external_id')->nullable()->after('keyword');
            $table->timestamp('parsed_at')->nullable()->after('source_external_id');
        });
    }

    public function down(): void
    {
        Schema::table('platform_suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'all_emails',
                'all_phones',
                'location',
                'contact_person',
                'established_year',
                'employee_count',
                'description',
                'main_products',
                'export_markets',
                'certifications',
                'keyword',
                'source_external_id',
                'parsed_at',
            ]);
        });
    }
};
