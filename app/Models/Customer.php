<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Customer
 *
 * @property $id
 * @property $name
 * @property $customer_group_id
 * @property $total_wet_weight
 * @property $total_dry_weight
 * @property $total_billing_weight
 * @property $total_edit_weight
 * @property $total_billing_payment
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Customer extends Model
{
  use HasFactory;

  static $rules = [
    'name' => 'required',
    'customer_group_id' => 'required',
    'total_wet_weight' => 'required',
    'total_dry_weight' => 'required',
    'total_billing_weight' => 'required',
    'total_edit_weight' => 'required',
    'total_billing_payment' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'customer_group_id', 'total_wet_weight', 'total_dry_weight', 'total_billing_weight', 'total_edit_weight', 'total_billing_payment'];

  public function group()
  {
    return $this->belongsTo(CustomerGroup::class);
  }
}
