<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoryStockLog
 *
 * @property $id
 * @property $inventory_id
 * @property $type
 * @property $quantity
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class InventoryStockLog extends Model
{
    
    static $rules = [
		'inventory_id' => 'required',
		'type' => 'required',
		'quantity' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['inventory_id','type','quantity'];



}
