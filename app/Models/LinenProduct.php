<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LinenProduct
 *
 * @property $id
 * @property $linen_type_id
 * @property $name
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class LinenProduct extends Model
{

  static $rules = [
    'linen_type_id' => 'required',
    'name' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['linen_type_id', 'name'];


  public function linenType()
  {
    return $this->belongsTo(LinenType::class);
  }
}
