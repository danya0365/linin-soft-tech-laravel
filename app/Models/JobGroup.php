<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JobGroup
 *
 * @property $id
 * @property $customer_id
 * @property $employee_id
 * @property $wet_weight
 * @property $dry_weight
 * @property $total_pieces
 * @property $operation_status
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class JobGroup extends Model
{

  static $rules = [
    'customer_id' => 'required',
    'employee_id' => 'required',
    'wet_weight' => 'required',
    'dry_weight' => 'required',
    'total_pieces' => 'required',
    'operation_status' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['customer_id', 'employee_id', 'wet_weight', 'dry_weight', 'total_pieces', 'operation_status'];

  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function jobs()
  {
    return $this->hasMany(Job::class);
  }

  public function pickUpEmployee()
  {
    return $this->belongsTo(Employee::class, 'pickup_employee_id');
  }

  public function packingEmployee()
  {
    return $this->belongsTo(Employee::class, 'packing_employee_id');
  }

  public function collectEmployee()
  {
    return $this->belongsTo(Employee::class, 'collect_employee_id');
  }

  /**
   * Convert the model instance to an array.
   *
   * @return array
   */
  public function toArray()
  {
    $array = parent::toArray();

    $array['operation_status_text'] = (function ($jobStatus) {
      $statusTexts = ['pickup' => 'รับสินค้า', 'progress' => 'ซัก, อบ, รีด', 'packing' => 'พับแพ็ค', 'collect' => 'จัดเก็บ', 'close' => 'ปิดงาน'];
      foreach ($statusTexts as $status => $text) {
        if (strtolower($status) == strtolower($jobStatus)) {
          return $text;
        }
      }
      return ucfirst($jobStatus);
    })($array['operation_status']);

    return $array;
  }

  public function packingSummaryReport()
  {
    $summaryReports = [];
    $totalValue = JobGroup::where('packing_employee_id', $this->packing_employee_id)->sum('total_pieces');
    $summaryReports[] = ['title' => 'จำนวนที่พับแพ็คแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }

  public function collectSummaryReport()
  {
    $summaryReports = [];
    $totalValue = JobGroup::where('collect_employee_id', $this->collect_employee_id)->sum('dry_weight');
    $summaryReports[] = ['title' => 'จำนวนที่จัดเก็บแล้ว', 'value' => $totalValue];
    return $summaryReports;
  }
}
