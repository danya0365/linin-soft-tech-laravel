<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class JobActivityLog
 *
 * @property $id
 * @property $job_id
 * @property $employee_id
 * @property $log_type
 * @property $old_value
 * @property $new_value
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class JobActivityLog extends Model
{
    
    static $rules = [
		'job_id' => 'required',
		'employee_id' => 'required',
		'log_type' => 'required',
		'old_value' => 'required',
		'new_value' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['job_id','employee_id','log_type','old_value','new_value'];



}
