<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {email? : Sign-in email} {--name= : Display name}';

    protected $description = 'Create or update the site owner account used to sign in at /admin';

    public function handle(): int
    {
        $email = $this->argument('email') ?: text('Email', required: true);
        $name = $this->option('name') ?: text('Name', default: config('portfolio.short_name'), required: true);
        $password = password('Password (min 12 characters)', required: true, validate: fn ($v) => strlen($v) < 12 ? 'Use at least 12 characters.' : null);

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => Hash::make($password)],
        );

        $this->components->info($user->wasRecentlyCreated
            ? "Created {$user->email}. Sign in at ".route('admin.login')
            : "Updated the password for {$user->email}.");

        return self::SUCCESS;
    }
}
