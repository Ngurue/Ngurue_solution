<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'district'];

    /**
     * Get all pigs registered in this village.
     */
    public function pigs(): HasMany
    {
        return $this->hasMany(Pig::class);
    }
}