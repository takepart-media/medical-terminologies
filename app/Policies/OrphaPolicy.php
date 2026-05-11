<?php

namespace App\Policies;

use App\Models\Orpha;
use App\Models\User;

class OrphaPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Orpha $orpha): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Orpha $orpha): bool
    {
        return false;
    }

    public function delete(User $user, Orpha $orpha): bool
    {
        return false;
    }
}
