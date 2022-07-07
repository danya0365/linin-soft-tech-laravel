<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set-password {args}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        list($id, $password) = explode(':', $this->argument('args'));
        Log::info("Start " . $this->description);
        Log::info('Set ID: ' . $id . ', password: ' . $password);
        User::where('id', $id)->update(['password' => Hash::make($password)]);
        Log::info("End " . $this->description);
    }
}
