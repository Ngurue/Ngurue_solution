<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('metric_code')->after('title')->nullable();
            $table->integer('value')->after('description')->default(0);
            $table->string('category')->after('status')->default('general');
        });
    }

    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn(['metric_code', 'value', 'category']);
        });
    }
};