<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Customer
 *
 * @property $id
 * @property $name
 * @property $customer_group_id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Customer extends Model
{
  use SoftDeletes;
  use HasFactory;

  static $rules = [
    'name' => 'required',
    'customer_group_id' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'customer_group_id'];

  public function group()
  {
    return $this->belongsTo(CustomerGroup::class);
  }

  public function customerGroup()
  {
    return $this->belongsTo(CustomerGroup::class);
  }

  public function users()
  {
    return $this->belongsToMany(User::class, 'users_customers')->using(UserCustomer::class);
  }
}