<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Truck
 *
 * @property $id
 * @property $name
 * @property $photo
 * @property $plate_number
 * @property $operation_id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Truck extends Model
{
  use HasFactory;

  static $rules = [
    'name' => 'required',
    'photo' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['name', 'photo', 'plate_number', 'operation_id'];

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
    })($array['operation_id']);

    return $array;
  }
}