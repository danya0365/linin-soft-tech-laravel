<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmployeeWorkingTime
 *
 * @property $id
 * @property $employee_id
 * @property $working_date
 * @property $time_duration
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class EmployeeWorkingTime extends Model
{
  use SoftDeletes;

  static $rules = [
    'employee_id' => 'required',
    'working_date' => 'required',
    'time_duration' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['employee_id', 'working_date', 'time_duration'];
}
