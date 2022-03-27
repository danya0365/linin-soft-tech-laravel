<?php

namespace App\Models;

use App\Translations\Translator;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Employee
 *
 * @property $id
 * @property $code
 * @property $password
 * @property $name
 * @property $photo
 * @property $department_id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Employee extends Model
{
  use HasFactory;

  static $rules = [
    'code' => 'required',
    'name' => 'required',
    'photo' => 'required',
    'department_id' => 'required',
  ];

  protected $perPage = 20;

  /**
   * Attributes that should be mass-assignable.
   *
   * @var array
   */
  protected $fillable = ['code', 'name', 'photo', 'department_id'];

  public function department()
  {
    return $this->belongsTo(Department::class);
  }

  function getTotalTimeDurationOfWorkingTime()
  {
    $totalTimeDurationOfWorkingTime = EmployeeWorkingTime::where('employee_id', $this->id)->sum('time_duration');
    $interval = CarbonInterval::seconds($totalTimeDurationOfWorkingTime)->cascade();
    $translator = new Translator();
    $interval->setLocalTranslator($translator);
    return $interval->forHumans();
  }
}
