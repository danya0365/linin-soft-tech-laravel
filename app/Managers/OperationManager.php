<?php

namespace App\Managers;

use App\Enums\OperationStatus;
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
        if (!$operation->customer_id) {
            return;
        }

        $operationDate = $operation->created_at->format('Y-m-d');

        $query = OperationLinenProduct::query()->select(
            DB::raw('sum(wet_weight) as total_wet_weight'),
            DB::raw('sum(dry_weight) as total_dry_weight'),
            DB::raw('sum(iron_piece) as total_iron_piece'),
            DB::raw('sum(packing_piece) as total_packing_piece'),
            DB::raw('sum(collect_weight) as total_collect_weight'),
            DB::raw('sum(collect_pack) as total_collect_pack'),
            DB::raw('sum(deliver_pack) as total_delivery_pack')
        )
            ->join('operations', 'operations.id', '=', 'operation_id');
        $query->where('operations.customer_id', $operation->customer_id);
        $query->where('operations.status', OperationStatus::Close());
        $query->whereBetween('operations_linen_products.created_at', [$operationDate . ' 00:00:00', $operationDate . ' 23:59:59']);
        $queryEdit = clone $query;
        $operationLinenProductSummary = $query->first();

        $operationLinenCase = OperationLinenCase::getEdit();
        $totalEditCollectWeightQuery = $queryEdit->where('operations_linen_products.linen_case', $operationLinenCase['var'])->first();

        /**
         * ============================================================================
         * ⚠️ ทำไมต้อง Query 2 ครั้ง?
         * ============================================================================
         * 
         * เนื่องจากระบบมี Operation 2 ประเภท ที่ใช้ "วันที่" ต่างกันในการนับ:
         * 
         * 1. Non-payment Operations (ซัก, อบ, รีด, แพ็ค, ส่ง ฯลฯ)
         *    - ใช้ "created_at" เป็นวันที่ที่ operation เกิดขึ้น
         *    - เช่น ซักผ้าวันที่ 1 มกราคม → นับเป็นวันที่ 1 มกราคม
         * 
         * 2. Payment Operations (เก็บเงิน/ออกบิล)
         *    - ใช้ "billing_payment_date" เป็นวันที่เก็บเงินจริง
         *    - created_at อาจเป็นวันที่สร้าง record ซึ่งอาจต่างจากวันที่เก็บเงิน
         *    - เช่น สร้างบิลวันที่ 1 มกราคม แต่เก็บเงินวันที่ 5 มกราคม 
         *      → ต้องนับเป็นวันที่ 5 มกราคม (ตามวันเก็บเงินจริง)
         * 
         * ถ้าใช้ created_at อย่างเดียว → ข้อมูล billing จะอยู่ผิดวัน ทำให้รายงานไม่ตรง
         * ============================================================================
         */

        // === Query 1: Non-payment operations ===
        // ใช้ created_at เพราะ non-payment operations (ซัก, อบ, รีด ฯลฯ) นับวันตาม created_at
        $nonPaymentQuery = Operation::query()->select(
            DB::raw('sum(total_billing_weight) as total_billing_weight'),
            DB::raw('sum(total_billing_payment) as total_billing_payment'),
            DB::raw('sum(total_edit_weight) as total_edit_weight'),
        );
        $nonPaymentQuery->where('customer_id', $operation->customer_id);
        $nonPaymentQuery->where('operation_type', '!=', 'payment');
        $nonPaymentQuery->whereBetween('created_at', [$operationDate . ' 00:00:00', $operationDate . ' 23:59:59']);
        $nonPaymentQuery->where('status', OperationStatus::Close());
        $nonPaymentSummary = $nonPaymentQuery->first();

        // === Query 2: Payment operations ===
        // ใช้ billing_payment_date เพราะ payment operations ต้องนับตามวันที่เก็บเงินจริง
        // ไม่ใช่วันที่สร้าง record (created_at)
        $paymentQuery = Operation::query()->select(
            DB::raw('sum(total_billing_weight) as total_billing_weight'),
            DB::raw('sum(total_billing_payment) as total_billing_payment'),
            DB::raw('sum(total_edit_weight) as total_edit_weight'),
        );
        $paymentQuery->where('customer_id', $operation->customer_id);
        $paymentQuery->where('operation_type', 'payment');
        $paymentQuery->whereDate('billing_payment_date', $operationDate);
        $paymentQuery->where('status', OperationStatus::Close());
        $paymentSummary = $paymentQuery->first();

        // === รวมผลลัพธ์จากทั้ง 2 กลุ่ม ===
        // เนื่องจากข้อมูลมาจากคนละ query ต้องนำมาบวกกัน
        $totalBillingWeight = ($nonPaymentSummary->total_billing_weight ?? 0) + ($paymentSummary->total_billing_weight ?? 0);
        $totalBillingPayment = ($nonPaymentSummary->total_billing_payment ?? 0) + ($paymentSummary->total_billing_payment ?? 0);
        $totalEditWeight = ($nonPaymentSummary->total_edit_weight ?? 0) + ($paymentSummary->total_edit_weight ?? 0);

        $operationLog = CustomerOperationDailySummary::where(['customer_id' => $operation->customer_id, 'operation_date' => $operationDate])->first();
        if (!$operationLog) {
            $operationLog = new CustomerOperationDailySummary;
            $operationLog->customer_id = $operation->customer_id;
            $operationLog->operation_date = $operationDate;
        }
        $operationLog->total_wet_weight = $operationLinenProductSummary->total_wet_weight ?? 0;
        $operationLog->total_dry_weight = $operationLinenProductSummary->total_dry_weight ?? 0;
        $operationLog->total_iron_piece = $operationLinenProductSummary->total_iron_piece ?? 0;
        $operationLog->total_packing_piece = $operationLinenProductSummary->total_packing_piece ?? 0;
        $operationLog->total_edit_collect_weight = $totalEditCollectWeightQuery->total_collect_weight ?? 0;
        $operationLog->total_edit_weight = $totalEditWeight;
        $operationLog->total_collect_weight = $operationLinenProductSummary->total_collect_weight ?? 0;
        $operationLog->total_collect_pack = $operationLinenProductSummary->total_collect_pack ?? 0;
        $operationLog->total_delivery_pack = $operationLinenProductSummary->total_delivery_pack ?? 0;
        $operationLog->total_billing_weight = $totalBillingWeight;
        $operationLog->total_billing_payment = $totalBillingPayment;
        $operationLog->save();
    }

    /**
     * Recalculate CustomerOperationDailySummary for a specific customer and date
     * Used when operations are deleted or modified
     * 
     * @param int $customerId
     * @param string $date (Y-m-d format)
     */
    public static function recalculateCustomerOperationDailySummary($customerId, $date)
    {
        $operationDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
        
        // ดึง Operation ทั้งหมดในวันนั้นที่ยังไม่ถูกลบ
        $operations = Operation::where('customer_id', $customerId)
            ->whereDate('created_at', $operationDate)
            ->where('status', OperationStatus::Close())
            ->get();
        
        if ($operations->isEmpty()) {
            // ถ้าไม่มี operation ในวันนั้นแล้ว ให้ลบ summary
            CustomerOperationDailySummary::where('customer_id', $customerId)
                ->where('operation_date', $operationDate)
                ->delete();
        } else {
            // คำนวณใหม่โดยใช้ operation แรกที่เจอ (จะ recalculate ทั้งวัน)
            self::createCustomerOperationDailySummary($operations->first());
        }
    }
}
