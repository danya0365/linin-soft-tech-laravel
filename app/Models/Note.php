<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

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
     * Cache key for existing tags
     */
    const CACHE_KEY_EXISTING_TAGS = 'note_existing_tags';
    
    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_TTL = 3600;

    /**
     * Boot the model - auto clear cache when note is created/updated/deleted
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when a note with tag is created
        static::created(function ($note) {
            if (!empty($note->tag)) {
                self::clearTagsCache();
            }
        });

        // Clear cache when a note's tag is updated
        static::updated(function ($note) {
            if ($note->isDirty('tag')) {
                self::clearTagsCache();
            }
        });

        // Clear cache when a note with tag is deleted
        static::deleted(function ($note) {
            if (!empty($note->tag)) {
                self::clearTagsCache();
            }
        });
    }

    /**
     * Get existing tags from cache or database
     * Returns unique tags that have been used before
     * Uses file cache by default (no Redis needed)
     */
    public static function getExistingTags(): array
    {
        return Cache::remember(self::CACHE_KEY_EXISTING_TAGS, self::CACHE_TTL, function () {
            return self::whereNotNull('tag')
                ->where('tag', '!=', '')
                ->distinct()
                ->orderBy('tag')
                ->pluck('tag')
                ->toArray();
        });
    }

    /**
     * Clear the existing tags cache
     * Call this when tags are modified
     */
    public static function clearTagsCache(): void
    {
        Cache::forget(self::CACHE_KEY_EXISTING_TAGS);
    }

    /**
     * Get tag color based on hash of tag name (for consistent colors)
     */
    public function getTagColorAttribute(): string
    {
        if (empty($this->tag)) {
            return '#6c757d';
        }
        
        // Generate a consistent color based on tag string hash
        $colors = [
            '#dc3545', // red
            '#fd7e14', // orange
            '#ffc107', // yellow
            '#28a745', // green
            '#20c997', // teal
            '#17a2b8', // cyan
            '#007bff', // blue
            '#6f42c1', // purple
            '#e83e8c', // pink
            '#6c757d', // gray
        ];
        
        $hash = crc32($this->tag);
        return $colors[abs($hash) % count($colors)];
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


