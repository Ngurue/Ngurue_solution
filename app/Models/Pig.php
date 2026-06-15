<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pig extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_number', 
        'sex', 
        'birthdate', 
        'breed', 
        'status', 
        'village_id', 
        'birth_weight', 
        'notes'
    ];

    /**
     * Get the village where this pig is located.
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Get all medical treatments given to this pig.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }
}