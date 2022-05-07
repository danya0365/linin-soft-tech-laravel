<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 *
 * @property $id
 * @property $inventory_group_id
 * @property $name
 * @property $unit
 * @property $total_quantity
 * @property $remain_quantity
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Inventory extends Model
{

  static $rules = [
    'inventory_group_id' => 'required',
    'name' => 'required',
    'unit' => 'required',
    'total_quantity' => 'required',
    'remain_quantity' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['inventory_group_id', 'name', 'unit', 'total_quantity', 'remain_quantity'];


  public function inventoryGroup()
  {
    return $this->belongsTo(InventoryGroup::class);
  }
}