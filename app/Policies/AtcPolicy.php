<?php

namespace App\Policies;

use App\Models\Atc;
use App\Models\User;

class AtcPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Atc $atc): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Atc $atc): bool
    {
        return false;
    }

    public function delete(User $user, Atc $atc): bool
    {
        return false;
    }
}
