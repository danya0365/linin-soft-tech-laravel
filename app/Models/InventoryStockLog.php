<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class InventoryStockLog
 *
 * @property $id
 * @property $employee_id
 * @property $inventory_id
 * @property $type
 * @property $quantity
 * @property $cost
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class InventoryStockLog extends Model
{
    use SoftDeletes;

    static $rules = [
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['employee_id','inventory_id','type','quantity','cost'];



}
