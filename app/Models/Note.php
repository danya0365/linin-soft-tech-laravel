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
 * @property $tag
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

    /**
     * Available tags for notes
     */
    const TAG_MAINTENANCE = 'maintenance';
    const TAG_PARTS = 'parts';
    const TAG_LABOR = 'labor';
    const TAG_SERVICE = 'service';
    const TAG_OTHERS = 'others';

    /**
     * Get all available tags with labels
     */
    public static function getAvailableTags(): array
    {
        return [
            self::TAG_MAINTENANCE => '🔧 ค่าซ่อมบำรุง',
            self::TAG_PARTS => '🔩 ค่าอะไหล่',
            self::TAG_LABOR => '👷 ค่าแรงช่าง',
            self::TAG_SERVICE => '🛠️ ค่าบริการ',
            self::TAG_OTHERS => '📦 อื่นๆ',
        ];
    }

    /**
     * Get tag label
     */
    public function getTagLabelAttribute(): string
    {
        $tags = self::getAvailableTags();
        return $tags[$this->tag] ?? $this->tag ?? '-';
    }

    /**
     * Get tag color for display
     */
    public function getTagColorAttribute(): string
    {
        $colors = [
            self::TAG_MAINTENANCE => '#dc3545', // red
            self::TAG_PARTS => '#fd7e14', // orange
            self::TAG_LABOR => '#6f42c1', // purple
            self::TAG_SERVICE => '#0dcaf0', // cyan
            self::TAG_OTHERS => '#6c757d', // gray
        ];
        return $colors[$this->tag] ?? '#6c757d';
    }

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
    protected $fillable = ['message', 'image_url', 'cost', 'tag', 'washing_machine_id', 'dryer_machine_id', 'truck_id'];


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

