<?php

namespace App\Policies;

use App\Models\Loinc;
use App\Models\User;

class LoincPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Loinc $loinc): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Loinc $loinc): bool
    {
        return false;
    }

    public function delete(User $user, Loinc $loinc): bool
    {
        return false;
    }
}
