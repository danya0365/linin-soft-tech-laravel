<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List admin users and reset password found by ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Searching for Admin users...');

        $admins = User::where('is_can_access_admin', true)
            ->orWhere('role', 'admin')
            ->get(['id', 'name', 'email', 'role', 'is_can_access_admin']);

        if ($admins->isEmpty()) {
            $this->error('No admin users found.');
            return 1;
        }

        $headers = ['ID', 'Name', 'Email', 'Role', 'Access Admin'];
        $this->table($headers, $admins->toArray());

        $userId = $this->ask('Enter the ID of the user to reset password');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $password = $this->ask('Enter new password (leave empty for random password)');

        // Generate random password if empty
        if (empty($password)) {
            $password = \Illuminate\Support\Str::random(12);
            $this->info("Generated random password: {$password}");
        } else {
            $confirmPassword = $this->secret('Confirm new password');

            if ($password !== $confirmPassword) {
                $this->error('Passwords do not match.');
                return 1;
            }

            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters.');
                return 1;
            }
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->newLine();
        $this->info("Password for user '{$user->name}' (ID: {$user->id}) has been reset successfully.");
        $this->warn("New Password: {$password}");
        $this->info("Please copy and save this password securely.");
        Log::info("Admin password reset via artisan for user ID: {$user->id}");

        return 0;
    }
}
