<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class OperationLog
 *
 * @property $id
 * @property $operation_id
 * @property $employee_id
 * @property $action_name
 * @property $old_values
 * @property $new_values
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class OperationLog extends Model
{
  use SoftDeletes;

  static $rules = [
    'operation_id' => 'required',
    'employee_id' => 'required',
    'action_name' => 'required',
    'old_values' => 'required',
    'new_values' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['operation_id', 'employee_id', 'action_name', 'old_values', 'new_values'];
}
