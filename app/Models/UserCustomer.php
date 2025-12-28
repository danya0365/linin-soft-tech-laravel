<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserCustomer
 *
 * @property $id
 * @property $user_id
 * @property $customer_id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class UserCustomer extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'user_id',
    'customer_id',
  ];

  static $onCreateRules = [
    'user_id' => ['required', 'integer'],
    'customer_id' => ['required', 'integer'],
  ];

  static $onUpdateRules = [
    'user_id' => ['required', 'integer'],
    'customer_id' => ['required', 'integer'],
  ];

  protected $perPage = 20;

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }
}