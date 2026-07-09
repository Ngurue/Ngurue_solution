<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Ruhusa ya usimamizi: admin anaona rekodi za wote
            $table->boolean('is_admin')->default(false)->after('email');
        });

        Schema::table('records', function (Blueprint $table) {
            // Umri ulioandikwa kwa mkono (mfano "2 Miezi") pale tarehe ya kuzaliwa haitoshi
            $table->string('age_manual')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });

        Schema::table('records', function (Blueprint $table) {
            $table->dropColumn('age_manual');
        });
    }
};
