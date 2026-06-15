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
       Schema::create('pigs', function (Blueprint $table) {
        $table->id();
        $table->string('tag_number')->unique(); // Unique ear tag ID
        $table->enum('sex', ['Male', 'Female', 'Castrated']);
        $table->date('birthdate');
        $table->string('breed')->nullable(); // e.g., Landrace, Large White, Duroc
        $table->enum('status', ['Piglet', 'Weaner', 'Grower', 'Sow', 'Boar', 'Sold', 'Deceased'])->default('Piglet');
        
        // This links the pig directly to an administrative village
        $table->foreignId('village_id')->nullable()->constrained()->onDelete('set null'); 
        
        $table->decimal('birth_weight', 5, 2)->nullable(); // Weight in kg
        $table->text('notes')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pigs');
    }
};
