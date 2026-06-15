<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'pig_id', 
        'treatment_type', 
        'medicine_name', 
        'dosage', 
        'treatment_date', 
        'remarks'
    ];

    /**
     * Get the pig that received this treatment.
     */
    public function pig(): BelongsTo
    {
        return $this->belongsTo(Pig::class);
    }
}