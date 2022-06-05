<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Note
 *
 * @property $id
 * @property $message
 * @property $image_url
 * @property $cost
 * @property $washing_machine_id
 * @property $dryer_machine_id
 * @property $truck_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
 *
 * @property DryerMachine $dryerMachine
 * @property Truck $truck
 * @property WashingMachine $washingMachine
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Note extends Model
{
    use SoftDeletes;

    static $rules = [
		'message' => 'required',
		'cost' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['message','image_url','cost','washing_machine_id','dryer_machine_id','truck_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function dryerMachine()
    {
        return $this->hasOne('App\Models\DryerMachine', 'id', 'dryer_machine_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function truck()
    {
        return $this->hasOne('App\Models\Truck', 'id', 'truck_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function washingMachine()
    {
        return $this->hasOne('App\Models\WashingMachine', 'id', 'washing_machine_id');
    }
    

}
