<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\Expense;

class ExpenseManager extends Manager
{
    public static function create($typeName, $tableName, $tableId, $amount)
    {
        $expense = new Expense();
        $expense->type_name = $typeName;
        $expense->table_name = $tableName;
        $expense->table_id = $tableId;
        $expense->amount = $amount;
        $expense->save();
    }
}
