<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class DryerMachine
 *
 * @property $id
 * @property $name
 * @property $photo
 * @property $maximum_weight
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DryerMachine extends Model
{
  use HasFactory;

  static $rules = [
    'name' => 'required',
    'photo' => 'required',
    'maximum_weight' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'photo', 'maximum_weight'];

  public function operation()
  {
    return $this->belongsTo(Operation::class);
  }

  /**
   * Convert the model instance to an array.
   *
   * @return array
   */
  public function toArray()
  {
    $array = parent::toArray();

    $array['status_text'] = (function ($jobId) {
      return $jobId ? 'ไม่ว่าง' : 'พร้อมใช้งาน';
    })($array['job_id']);

    return $array;
  }
}
