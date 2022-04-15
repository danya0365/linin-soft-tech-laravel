<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EnergyResourceLog
 *
 * @property $id
 * @property $energy_resource_id
 * @property $employee_id
 * @property $value
 * @property $unit
 * @property $lot_number
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class EnergyResourceLog extends Model
{
    
    static $rules = [
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['energy_resource_id','employee_id','value','unit','lot_number'];



}
