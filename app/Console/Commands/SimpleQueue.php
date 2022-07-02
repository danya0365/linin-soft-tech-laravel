<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SimpleQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:simple';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple queue';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Log::channel('queue')->info("Start " . $this->description);
        while (true) {

            Log::channel('queue')->info("loop start: " . \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
            sleep(1);
        }
        Log::channel('queue')->info("End " . $this->description);
    }
}
