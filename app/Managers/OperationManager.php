<?php

namespace App\Managers;

use App\Managers\Manager;
use App\Models\CustomerOperationDailySummary;
use App\Models\Operation;
use App\Models\OperationLinenCase;
use App\Models\OperationLinenProduct;
use App\Models\OperationLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperationManager extends Manager
{
    public static function createOperationLog($employeeId, Operation $operation, $actionName, $oldValues, $newValues)
    {
        $newValueKeys = array_keys($newValues);
        $oldValues = array_filter($oldValues, fn ($key) => in_array($key, $newValueKeys), ARRAY_FILTER_USE_KEY);
        $operationLog = new OperationLog();
        $operationLog->employee_id = $employeeId;
        $operationLog->operation_id = $operation->id;
        $operationLog->action_name = $actionName;
        $operationLog->old_values = json_encode($oldValues);
        $operationLog->new_values = json_encode($newValues);
        $operationLog->save();
    }

    public static function generateDailyReport()
    {
        $now = Carbon::now();
        $operations = Operation::whereDate($now)->get();
        foreach ($operations as $operation) {
            self::createCustomerOperationDailySummary($operation);
        }
    }

    public static function generateReportByDate($date)
    {
        $operations = Operation::whereDate('created_at', $date)->get();
        foreach ($operations as $operation) {
            self::createCustomerOperationDailySummary($operation);
        }
    }

    public static function createCustomerOperationDailySummary(Operation $operation)
    {
        if (!$operation->customer) {
            return;
        }

        $operationDate = $operation->created_at->format('Y-m-d');

        $query = OperationLinenProduct::query()->select(
            DB::raw('sum(wet_weight) as total_wet_weight'),
            DB::raw('sum(dry_weight) as total_dry_weight'),
            DB::raw('sum(iron_piece) as total_iron_piece'),
            DB::raw('sum(packing_piece) as total_packing_piece'),
            DB::raw('sum(collect_weight) as total_collect_weight'),
            DB::raw('sum(operations.total_billing_weight) as total_billing_weight')
        )
            ->join('operations', 'operations.id', '=', 'operation_id');
        $query->where('operations.customer_id', $operation->customer->id);
        $query->whereBetween('operations_linen_products.created_at', [$operationDate . ' 00:00:00', $operationDate . ' 23:59:59']);
        $queryEdit = clone $query;
        $operationLinenProduct = $query->first();

        $operationLinenCase = OperationLinenCase::getEdit();
        $totalEditCollectWeightQuery = $queryEdit->where('operations_linen_products.linen_case', $operationLinenCase['var'])->first();

        $operationLog = CustomerOperationDailySummary::where(['customer_id' => $operation->customer->id, 'operation_date' => $operationDate])->first();
        if (!$operationLog) {
            $operationLog = new CustomerOperationDailySummary;
            $operationLog->customer_id = $operation->customer->id;
            $operationLog->operation_date = $operationDate;
        }
        $operationLog->total_wet_weight = $operationLinenProduct->total_wet_weight;
        $operationLog->total_dry_weight = $operationLinenProduct->total_dry_weight;
        $operationLog->total_iron_piece = $operationLinenProduct->total_iron_piece;
        $operationLog->total_packing_piece = $operationLinenProduct->total_packing_piece;
        $operationLog->total_edit_collect_weight = $totalEditCollectWeightQuery->total_collect_weight ?? 0;
        $operationLog->total_collect_weight = $operationLinenProduct->total_collect_weight;
        $operationLog->total_billing_weight = $operationLinenProduct->total_billing_weight ?? 0;
        $operationLog->save();
    }
}
