<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\Income;

class IncomeManager extends Manager
{
    public static function create($typeName, $modelInstance, $amount)
    {
        $income = new Income();
        $income->type_name = $typeName;
        $income->table_name = $modelInstance->getTable();;
        $income->table_id = $modelInstance->id;
        $income->amount = $amount;
        $income->save();
    }

    public static function delete($modelInstance)
    {
        $tableName = $modelInstance->getTable();
        $tableId = $modelInstance->id;
        Income::where('table_name', $tableName)->where('table_id', $tableId)->delete();
    }
}
