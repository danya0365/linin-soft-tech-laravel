<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LoginHistory
 *
 * @property $id
 * @property $user_id
 * @property $name
 * @property $email
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class LoginHistory extends Model
{
    
    static $rules = [
		'user_id' => 'required',
		'name' => 'required',
		'email' => 'required',
    ];

    protected $perPage = 20;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','name','email'];



}
