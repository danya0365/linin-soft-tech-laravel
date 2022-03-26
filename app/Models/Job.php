<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Job
 *
 * @property $id
 * @property $job_group_id
 * @property $customer_id
 * @property $employee_id
 * @property $job_type
 * @property $washing_machine_id
 * @property $dryer_machine_id
 * @property $linen_type_id
 * @property $wet_weight
 * @property $color
 * @property $status
 * @property $tags
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Job extends Model
{

  static $rules = [
    'job_group_id' => 'required',
    'customer_id' => 'required',
    'employee_id' => 'required',
    'job_type' => 'required',
    'status' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['job_group_id', 'customer_id', 'employee_id', 'job_type', 'washing_machine_id', 'dryer_machine_id', 'linen_type_id', 'wet_weight', 'color', 'status', 'tags'];


  public function employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function jobGroup()
  {
    return $this->belongsTo(JobGroup::class);
  }

  public function washingMachine()
  {
    return $this->belongsTo(WashingMachine::class);
  }

  public function linenType()
  {
    return $this->belongsTo(LinenType::class);
  }

  /**
   * Convert the model instance to an array.
   *
   * @return array
   */
  public function toArray()
  {
    $array = parent::toArray();

    $array['job_case'] = (function ($jobVar) {
      foreach (JobCase::$list as $jobCase) {
        if ($jobCase['var'] == $jobVar) {
          return $jobCase;
        }
      }
      return $jobVar;
    })($array['job_case']);

    return $array;
  }
}
