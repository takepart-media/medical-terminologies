<?php

namespace App\Policies;

use App\Models\Icd10;
use App\Models\User;

class Icd10Policy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Icd10 $icd10): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Icd10 $icd10): bool
    {
        return false;
    }

    public function delete(User $user, Icd10 $icd10): bool
    {
        return false;
    }
}
