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
}
