<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Operation
 *
 * @property $id
 * @property $operation_type
 * @property $employee_id
 * @property $customer_id
 * @property $wash_employee_id
 * @property $dry_employee_id
 * @property $iron_employee_id
 * @property $packing_employee_id
 * @property $collect_employee_id
 * @property $job_case
 * @property $washing_machine_id
 * @property $dryer_machine_id
 * @property $total_wet_weight
 * @property $total_dry_weight
 * @property $total_iron_piece
 * @property $total_packing_piece
 * @property $colors
 * @property $search_tags
 * @property $status
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Operation extends Model
{

  static $rules = [
    'operation_type' => 'required',
    'employee_id' => 'required',
    'status' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['operation_type', 'employee_id', 'customer_id', 'wash_employee_id', 'dry_employee_id', 'iron_employee_id', 'packing_employee_id', 'collect_employee_id', 'job_case', 'washing_machine_id', 'dryer_machine_id', 'total_wet_weight', 'total_dry_weight', 'total_iron_piece', 'total_packing_piece', 'colors', 'search_tags', 'status'];


  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function washingMachine()
  {
    return $this->belongsTo(WashingMachine::class);
  }

  public function linenProducts()
  {
    return $this->belongsToMany(LinenProduct::class, 'operations_linen_products')->using(OperationLinenProduct::class);
  }

  public function washEmployee()
  {
    return $this->belongsTo(Employee::class, 'wash_employee_id');
  }

  public function dryerMachine()
  {
    return $this->belongsTo(DryerMachine::class);
  }

  public function dryEmployee()
  {
    return $this->belongsTo(Employee::class, 'dry_employee_id');
  }

  public function ironEmployee()
  {
    return $this->belongsTo(Employee::class, 'iron_employee_id');
  }

  public function generateSearchTag()
  {
    $searchTags = [];
    foreach ($this->linenProducts as $linenProduct) {
      $searchTags[] = $linenProduct->pivot->linen_case;
      $searchTags[] = $linenProduct->pivot->color;
    }
    $this->search_tags = $searchTags;
    $this->save();
  }

  /**
   * Convert the model instance to an array.
   *
   * @return array
   */
  public function toArray()
  {
    $array = parent::toArray();
    return $array;
  }

  public function washSummaryReport()
  {
    $totalValue = 0;
    $summaryReports = [];
    $operationLinenProducts = DB::table('operations_linen_products')
      ->selectRaw(
        'SUM(wet_weight) as total_wet_weight, linen_product_id, linen_products.name as linen_product_name'
      )
      ->join('linen_products', function ($join) {
        $join->on('linen_products.id', '=', 'operations_linen_products.linen_product_id');
      })
      ->join('operations', function ($join) {
        $join->on('operations.id', '=', 'operations_linen_products.operation_id');
      })
      ->groupBy('operations_linen_products.linen_product_id')
      ->where('operations.employee_id', $this->wash_employee_id)
      ->orderBy('total_wet_weight', 'desc')->get();

    foreach ($operationLinenProducts as $operationLinenProduct) {
      $totalValue += $operationLinenProduct->total_wet_weight;
      $summaryReports[] = ['title' => $operationLinenProduct->linen_product_name, 'value' => $operationLinenProduct->total_wet_weight];
    }

    $summaryReports[] = ['title' => 'จำนวนที่ซักแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }
}
