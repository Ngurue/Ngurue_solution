<?php

namespace App\Policies;

use App\Models\Record;
use App\Models\User;

class RecordPolicy
{
    /**
     * Admin anaruhusiwa kufanya kila kitu (Super access).
     * Ikirudi `true`, ukaguzi mwingine unarukwa.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->is_admin ? true : null;
    }

    /**
     * Mtumiaji anaweza kuona rekodi yake mwenyewe.
     */
    public function view(User $user, Record $record): bool
    {
        return $user->id === $record->user_id;
    }

    /**
     * Mtumiaji anaweza kuhariri rekodi yake mwenyewe.
     */
    public function update(User $user, Record $record): bool
    {
        return $user->id === $record->user_id;
    }

    /**
     * Mtumiaji anaweza kufuta rekodi yake mwenyewe.
     */
    public function delete(User $user, Record $record): bool
    {
        return $user->id === $record->user_id;
    }
}
