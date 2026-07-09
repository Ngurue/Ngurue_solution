<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'record_type', 'title', 'pig_code', 'breed', 'gender', 'castration_status',
        'description', 'birth_date', 'age_manual', 'weaning_date', 'value', 'litter_size',
        'status', 'pen_number', 'sire_code', 'dam_code', 'weight_history', 'user_id',
    ];

    /**
     * Seti ya ubadilishaji wa data (Casts)
     */
    protected $casts = [
        'weight_history' => 'array', // Inageuza JSON kwenda Array na kinyume chake kiotomatiki
        'birth_date' => 'date',
        'weaning_date' => 'date',
    ];

    /**
     * Uhusiano: Nguruwe anamilikiwa na Mtumiaji (User) mmoja
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Rudisha rekodi ambazo mtumiaji anaruhusiwa kuona.
     * Admin anaona zote; mtumiaji wa kawaida anaona zake mwenyewe tu.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->is_admin) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    /**
     * Uhusiano: Kupata data za Baba mzazi
     */
    public function father()
    {
        return $this->belongsTo(Record::class, 'sire_code', 'pig_code');
    }

    /**
     * Uhusiano: Kupata data za Mama mzazi
     */
    public function mother()
    {
        return $this->belongsTo(Record::class, 'dam_code', 'pig_code');
    }

    /**
     * Uhusiano ulioboreshwa: Kupata watoto wote wa huyu nguruwe (kama ni Mama au Baba)
     * Kutumia Advanced Wheres (Closure) inalinda usalama wa query isivuje data za watu wengine.
     */
    public function children()
    {
        return $this->hasMany(Record::class, 'dam_code', 'pig_code')
            ->orWhere(function ($query) {
                $query->where('sire_code', $this->pig_code);
            });
    }
}
