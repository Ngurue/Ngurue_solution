<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $blueprint) {
            $blueprint->id();
            
            // HAPA NDIPO UNAPOBADILISHA: Tumeongeza ->nullable() mwishoni
            $blueprint->string('title')->nullable();
            
            $blueprint->text('description')->nullable();
            $blueprint->string('status')->default('pending');
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};