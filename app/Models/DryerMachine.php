<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;
    use HasFactory;

    public static $rules = [
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
    protected $fillable = ['name', 'photo', 'maximum_weight', 'service_status'];

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

        if ($this->service_status === 'broken') {
            $array['status_text'] = 'เสีย/ซ่อม';
        } elseif ($this->operation_id) {
            $array['status_text'] = 'กำลังใช้งาน';
        } else {
            $array['status_text'] = 'พร้อมใช้งาน';
        }

        return $array;
    }
}
