<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;

class OptimizeExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expense:optimize';

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
        // Create a new Table instance.
        $table = new Table($this->output);

        // Set the table headers.
        $table->setHeaders([
            'Site', 'Description'
        ]);

        // Set the contents of the table.
        $table->setRows([
            ['https://laravel.com',        'The official Laravel website'],
            ['https://forge.laravel.com/', 'Painless PHP Servers'],
            ['https://envoyer.io/',        'Zero Downtime PHP Deployment']
        ]);

        // Render the table to the output.
        $table->render();
    }
}