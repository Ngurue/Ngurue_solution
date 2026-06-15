<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('records', function (Blueprint $table) {
            // Tunabadilisha muundo uendane na ufugaji
            $table->string('pig_code')->after('id')->unique();
            $table->string('breed')->after('title'); // Aina (mfano: Large White)
            $table->string('gender')->after('breed'); // Jinsia: Male/Female
            $table->date('birth_date')->after('description')->nullable(); // Tarehe ya kuzaliwa
            $table->string('pen_number')->after('status'); // Namba ya Banda
            $table->string('sire_code')->after('pen_number')->nullable(); // ID ya Baba
            $table->string('dam_code')->after('sire_code')->nullable(); // ID ya Mama
        });
    }

    public function down(): void
    {
        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn(['pig_code', 'breed', 'gender', 'birth_date', 'pen_number', 'sire_code', 'dam_code']);
        });
    }
};