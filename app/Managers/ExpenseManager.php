<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\Expense;

class ExpenseManager extends Manager
{
    public static function create($typeName, $modelInstance, $amount)
    {
        $expense = new Expense();
        $expense->type_name = $typeName;
        $expense->table_name = $modelInstance->getTable();
        $expense->table_id = $modelInstance->id;
        $expense->amount = $amount;
        $expense->save();
    }

    public static function delete($modelInstance)
    {
        $tableName = $modelInstance->getTable();
        $tableId = $modelInstance->id;
        Expense::where('table_name', $tableName)->where('table_id', $tableId)->delete();
    }
}
