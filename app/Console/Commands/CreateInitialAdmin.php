<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mt:create-initial-admin')]
#[Description('Create the initial admin user')]
class CreateInitialAdmin extends Command
{
    public function handle(): int
    {
        $email = 'admin@medical-terminologies';

        if (User::where('email', $email)->exists()) {
            $this->warn("Admin user already exists: {$email}");

            return self::SUCCESS;
        }

        $admin = User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => 'password',
            'email_verified_at' => now(),
        ]);

        $this->info("Admin user created: {$admin->email}. Password: password");

        return self::SUCCESS;
    }
}
