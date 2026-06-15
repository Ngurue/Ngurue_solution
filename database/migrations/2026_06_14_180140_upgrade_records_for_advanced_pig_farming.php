<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->string('record_type')->default('pig')->after('id'); // pig au litter
            $table->string('castration_status')->nullable()->after('gender'); // Castrated, Intact, N/A
            $table->date('weaning_date')->nullable()->after('birth_date'); // Tarehe ya kuachishwa kunyonya
            $table->integer('litter_size')->nullable()->after('value'); // Idadi ya watoto waliozaliwa kundi moja
        });
    }

    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn(['record_type', 'castration_status', 'weaning_date', 'litter_size']);
        });
    }
};