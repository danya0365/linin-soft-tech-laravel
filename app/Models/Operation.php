<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Translations\Translator;
use Carbon\CarbonInterval;

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
    return $this->belongsToMany(LinenProduct::class, 'operations_linen_products')->using(OperationLinenProduct::class)->withPivot('linen_case', 'color', 'wet_weight', 'dry_weight', 'iron_piece', 'packing_piece', 'collect_weight');
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

  public function packingEmployee()
  {
    return $this->belongsTo(Employee::class, 'packing_employee_id');
  }

  public function collectEmployee()
  {
    return $this->belongsTo(Employee::class, 'collect_employee_id');
  }

  public function deliverEmployee()
  {
    return $this->belongsTo(Employee::class, 'deliver_employee_id');
  }

  public function deliverOperationLinenProducts()
  {
    return $this->hasMany(OperationLinenProduct::class, 'deliver_operation_id');
  }

  public function updateRelateFields()
  {
    $operation = self::with('linenProducts')->where('id', $this->id)->first();
    $searchTags = [];
    $colors = [];
    $totalWetWeight = $totalDryWeight = $totalIronPiece = $totalPackingPiece = $totalCollectWeight = 0;
    foreach ($operation->linenProducts as $linenProduct) {
      $searchTags[] = $linenProduct->pivot->linen_case;
      $searchTags[] = $linenProduct->pivot->color;
      $colors[] = $linenProduct->pivot->color;
      $totalWetWeight += $linenProduct->pivot->wet_weight;
      $totalDryWeight += $linenProduct->pivot->dry_weight;
      $totalIronPiece += $linenProduct->pivot->iron_piece;
      $totalPackingPiece += $linenProduct->pivot->packing_piece;
      $totalCollectWeight += $linenProduct->pivot->collect_weight;
    }
    $this->search_tags = $searchTags;
    $this->colors = $colors;
    $this->total_wet_weight = $totalWetWeight;
    $this->total_dry_weight = $totalDryWeight;
    $this->total_iron_piece = $totalIronPiece;
    $this->total_packing_piece = $totalPackingPiece;
    $this->total_collect_weight = $totalCollectWeight;
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

  public function drySummaryReport()
  {
    $totalValue = 0;
    $summaryReports = [];
    $operationLinenProducts = DB::table('operations_linen_products')
      ->selectRaw(
        'SUM(dry_weight) as total_dry_weight, linen_product_id, linen_products.name as linen_product_name'
      )
      ->join('linen_products', function ($join) {
        $join->on('linen_products.id', '=', 'operations_linen_products.linen_product_id');
      })
      ->join('operations', function ($join) {
        $join->on('operations.id', '=', 'operations_linen_products.operation_id');
      })
      ->groupBy('operations_linen_products.linen_product_id')
      ->where('operations.employee_id', $this->dry_employee_id)
      ->orderBy('total_dry_weight', 'desc')->get();

    foreach ($operationLinenProducts as $operationLinenProduct) {
      $totalValue += $operationLinenProduct->total_dry_weight;
      $summaryReports[] = ['title' => $operationLinenProduct->linen_product_name, 'value' => $operationLinenProduct->total_dry_weight];
    }

    $summaryReports[] = ['title' => 'จำนวนที่อบแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }

  public function ironSummaryReport()
  {
    $totalValue = 0;
    $summaryReports = [];
    $operationLinenProducts = DB::table('operations_linen_products')
      ->selectRaw(
        'SUM(iron_piece) as total_iron_piece, linen_product_id, linen_products.name as linen_product_name'
      )
      ->join('linen_products', function ($join) {
        $join->on('linen_products.id', '=', 'operations_linen_products.linen_product_id');
      })
      ->join('operations', function ($join) {
        $join->on('operations.id', '=', 'operations_linen_products.operation_id');
      })
      ->groupBy('operations_linen_products.linen_product_id')
      ->where('operations.employee_id', $this->iron_employee_id)
      ->orderBy('total_iron_piece', 'desc')->get();

    foreach ($operationLinenProducts as $operationLinenProduct) {
      $totalValue += $operationLinenProduct->total_iron_piece;
      $summaryReports[] = ['title' => $operationLinenProduct->linen_product_name, 'value' => $operationLinenProduct->total_iron_piece];
    }

    $summaryReports[] = ['title' => 'จำนวนที่รีดแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }

  public function packingSummaryReport()
  {
    $totalValue = 0;
    $summaryReports = [];
    $operationLinenProducts = DB::table('operations_linen_products')
      ->selectRaw(
        'SUM(packing_piece) as total_packing_piece, linen_product_id, linen_products.name as linen_product_name'
      )
      ->join('linen_products', function ($join) {
        $join->on('linen_products.id', '=', 'operations_linen_products.linen_product_id');
      })
      ->join('operations', function ($join) {
        $join->on('operations.id', '=', 'operations_linen_products.operation_id');
      })
      ->groupBy('operations_linen_products.linen_product_id')
      ->where('operations.employee_id', $this->packing_employee_id)
      ->orderBy('total_packing_piece', 'desc')->get();

    foreach ($operationLinenProducts as $operationLinenProduct) {
      $totalValue += $operationLinenProduct->total_packing_piece;
      $summaryReports[] = ['title' => $operationLinenProduct->linen_product_name, 'value' => $operationLinenProduct->total_packing_piece];
    }

    $summaryReports[] = ['title' => 'จำนวนที่พับแพ็คแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }

  public function collectSummaryReport()
  {
    $totalValue = 0;
    $summaryReports = [];
    $operationLinenProducts = DB::table('operations_linen_products')
      ->selectRaw(
        'SUM(collect_weight) as total_collect_weight, linen_product_id, linen_products.name as linen_product_name'
      )
      ->join('linen_products', function ($join) {
        $join->on('linen_products.id', '=', 'operations_linen_products.linen_product_id');
      })
      ->join('operations', function ($join) {
        $join->on('operations.id', '=', 'operations_linen_products.operation_id');
      })
      ->groupBy('operations_linen_products.linen_product_id')
      ->where('operations.employee_id', $this->collect_employee_id)
      ->orderBy('total_collect_weight', 'desc')->get();

    foreach ($operationLinenProducts as $operationLinenProduct) {
      $totalValue += $operationLinenProduct->total_collect_weight;
      $summaryReports[] = ['title' => $operationLinenProduct->linen_product_name, 'value' => $operationLinenProduct->total_collect_weight];
    }

    $summaryReports[] = ['title' => 'จำนวนที่จัดเก็บแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }

  public function timeDuration()
  {
    $timeDuration = (function () {
      $intervalInSeconds = 0;
      if ($this->created_at && $this->updated_at) {
        $intervalDiff = $this->created_at->diff($this->updated_at);
        $intervalInSeconds = (function (\DateInterval $interval) {
          return $interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s;
        })($intervalDiff);
      }
      return $intervalInSeconds;
    })();
    $interval = CarbonInterval::seconds($timeDuration)->cascade();
    $translator = new Translator();
    $interval->setLocalTranslator($translator);
    return $interval->forHumans();
  }
}