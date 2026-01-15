<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user with custom credentials';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Create New User');

        $name = $this->ask('Name');
        $email = $this->ask('Email');

        // Check availability
        if (User::where('email', $email)->exists()) {
            $this->error("User with email '{$email}' already exists.");
            return 1;
        }

        $role = $this->ask('Role', 'user');
        $password = $this->secret('Password');
        $confirmPassword = $this->secret('Confirm Password');

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match.');
            return 1;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return 1;
        }

        $isAdmin = $this->confirm('Access Admin?', false);
        $isManager = $this->confirm('Access Manager?', false);
        $isSupervisor = $this->confirm('Access Supervisor?', false);
        $isCustomer = $this->confirm('Access Customer?', false);
        $isWorker = $this->confirm('Access Worker?', false);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'is_can_access_admin' => $isAdmin,
            'is_can_access_manager' => $isManager,
            'is_can_access_supervisor' => $isSupervisor,
            'is_can_access_customer' => $isCustomer,
            'is_can_access_worker' => $isWorker,
        ]);

        $this->info("User '{$user->name}' (ID: {$user->id}) created successfully.");
        Log::info("User created via artisan: {$user->email} (ID: {$user->id})");

        return 0;
    }
}
