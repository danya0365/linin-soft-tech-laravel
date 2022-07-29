<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CustomerOperationDailySummary
 *
 * @property $id
 * @property $customer_id
 * @property $operation_date
 * @property $total_wet_weight
 * @property $total_dry_weight
 * @property $total_iron_piece
 * @property $total_packing_piece
 * @property $total_edit_collect_weight
 * @property $total_collect_weight
 * @property $total_billing_weight
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CustomerOperationDailySummary extends Model
{

  static $rules = [
    'customer_id' => 'required',
    'operation_date' => 'required',
    'total_wet_weight' => 'required',
    'total_dry_weight' => 'required',
    'total_iron_piece' => 'required',
    'total_packing_piece' => 'required',
    'total_edit_collect_weight' => 'required',
    'total_collect_weight' => 'required',
    'total_billing_weight' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['customer_id', 'operation_date', 'total_wet_weight', 'total_dry_weight', 'total_iron_piece', 'total_packing_piece', 'total_edit_collect_weight', 'total_collect_weight', 'total_billing_weight'];

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}
