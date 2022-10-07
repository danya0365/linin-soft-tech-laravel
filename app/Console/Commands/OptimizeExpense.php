<?php

namespace App\Console\Commands;

use App\Models\EnergyResourceLog;
use App\Models\Expense;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

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
        //SELECT COUNT(*), CONCAT(`table_name`, "-", `table_id`) as tableName FROM `expenses` GROUP BY tableName HAVING COUNT(*) > 1;

        // Create a new Table instance.
        $table = new Table($this->output);

        // Create a new TableSeparator instance.
        $separator = new TableSeparator;

        // Set the table headers.
        $table->setHeaders([
            'Log ID', 'Resource Name', 'Cost', '', 'Expense Value'
        ]);

        $rows = [];

        $energyResourceLogs = EnergyResourceLog::query()->with("energyResource")->whereNotNull('cost')->take(1000)->get();
        foreach ($energyResourceLogs as $key => $energyResourceLog) {

            $tableName = app(EnergyResourceLog::class)->getTable();
            $expense = Expense::where("table_name", $tableName)->where("table_id", $energyResourceLog->id)->first();
            $expenseAmount = $expense->amount ?? 0;
            $rows[] = [
                $energyResourceLog->id,
                $energyResourceLog->energyResource->name ?? "<error>Not found</error>",
                $energyResourceLog->cost,
                $energyResourceLog->cost == $expenseAmount ? "<info>==</info>" : "<error><></error>",
                $expense ? "<info>{$expense->amount}</info>" : "<error>Not found</error>"
            ];

            if (($key + 1) % 10 === 0) {
                $rows[] = $separator;
            }
        }

        // Set the contents of the table.
        $table->setRows($rows);

        // Render the table to the output.
        $table->render();
    }
}