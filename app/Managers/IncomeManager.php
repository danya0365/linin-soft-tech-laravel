<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\Income;

class IncomeManager extends Manager
{
    public static function create($typeName, $modelInstance, $amount, $date = null)
    {
        $income = new Income();
        $income->type_name = $typeName;
        $income->table_name = $modelInstance->getTable();
        $income->table_id = $modelInstance->id;
        $income->amount = $amount;
        if ($date) {
            $income->timestamps = false;
            $income->created_at = \Carbon\Carbon::parse($date);
            $income->updated_at = \Carbon\Carbon::now();
        }
        $income->save();
    }

    public static function delete($modelInstance)
    {
        $tableName = $modelInstance->getTable();
        $tableId = $modelInstance->id;
        Income::where('table_name', $tableName)->where('table_id', $tableId)->delete();
    }
}
