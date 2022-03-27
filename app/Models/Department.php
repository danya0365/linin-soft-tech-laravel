<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Department
 *
 * @property $id
 * @property $var_name
 * @property $name
 * @property $input_unit
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Department extends Model
{

  static $rules = [
    'name' => 'required',
    'input_unit' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['var_name', 'name', 'input_unit'];

  public function employees()
  {
    return $this->hasMany(Employee::class);
  }
}
