<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class DepartmentDailyCostLog
 *
 * @property $id
 * @property $department_id
 * @property $daily_date
 * @property $cost
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DepartmentDailyCostLog extends Model
{
  use SoftDeletes;

  static $rules = [
    'department_id' => 'required',
    'daily_date' => 'required',
    'cost' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['department_id', 'daily_date', 'cost'];

  public function department()
  {
    return $this->belongsTo(Department::class);
  }
}
