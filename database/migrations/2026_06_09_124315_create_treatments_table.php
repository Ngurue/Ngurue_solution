<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            
            // This links the treatment directly to a specific pig.
            // If that pig is deleted, its medical records are cleaned up automatically (cascade).
            $table->foreignId('pig_id')->constrained()->onDelete('cascade'); 
            
            $table->string('treatment_type'); // e.g., Vaccine, Deworming, Injury, Illness
            $table->string('medicine_name');   // e.g., Iron injection, Ivermectin, Penicillin
            $table->string('dosage')->nullable(); // e.g., "2ml", "1 tablet"
            $table->date('treatment_date');      // When the treatment was given
            $table->text('remarks')->nullable();  // Any extra notes (e.g., "Follow up in 14 days")
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};