<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AddTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add test user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::info("Start " . $this->description);
        DB::table('users')->insert([
            'name' => 'Test',
            'email' => 'test.linensofttech@gmail.com',
            'role' => 'test',
            'password' => Hash::make('test'),
            'created_at' =>  \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
        Log::info("End " . $this->description);
    }
}
