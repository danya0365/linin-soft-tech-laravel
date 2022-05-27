<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\Income;

class IncomeManager extends Manager
{
    public static function create($typeName, $tableName, $tableId, $amount)
    {
        $income = new Income();
        $income->type_name = $typeName;
        $income->table_name = $tableName;
        $income->table_id = $tableId;
        $income->amount = $amount;
        $income->save();
    }
}
