<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Output\ConsoleOutput;

class SetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set user password';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->ask('Enter user id');
        $password = $this->secret('Enter user password');
        $output = new ConsoleOutput();
        $output->writeln('Start ' . $this->description);
        $output->writeln('Set ID: ' . $id . ', password: ' . $password);
        User::where('id', $id)->update(['password' => Hash::make($password)]);
        $output->writeln('End ' . $this->description);
    }
}
