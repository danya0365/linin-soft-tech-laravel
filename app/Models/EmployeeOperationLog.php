<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class EmployeeOperationLog
 *
 * @property $id
 * @property $employee_id
 * @property $operation_type
 * @property $action_type
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class EmployeeOperationLog extends Model
{
    
    static $rules = [
		'employee_id' => 'required',
		'operation_type' => 'required',
		'action_type' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['employee_id','operation_type','action_type'];



}
