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
 * @property array $tags
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
     * Cast attributes to native types
     */
    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * Boot the model - auto clear cache when note is created/updated/deleted
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when a note with tags is created
        static::created(function ($note) {
            if (!empty($note->tags)) {
                self::clearCacheForNote($note);
            }
        });

        // Clear cache when a note's tags is updated
        static::updated(function ($note) {
            if ($note->isDirty('tags')) {
                self::clearCacheForNote($note);
            }
        });

        // Clear cache when a note with tags is deleted
        static::deleted(function ($note) {
            if (!empty($note->tags)) {
                self::clearCacheForNote($note);
            }
        });
    }

    /**
     * Clear cache for a specific note (determines machine type automatically)
     */
    private static function clearCacheForNote($note): void
    {
        // Determine which machine this note belongs to
        if ($note->washing_machine_id) {
            self::clearTagsCache('washing_machine', $note->washing_machine_id);
        } elseif ($note->dryer_machine_id) {
            self::clearTagsCache('dryer_machine', $note->dryer_machine_id);
        } elseif ($note->truck_id) {
            self::clearTagsCache('truck', $note->truck_id);
        } else {
            // Just clear global cache if no machine specified
            self::clearTagsCache();
        }
    }

    /**
     * Get all existing tags from cache or database
     * Returns unique tags that have been used before (flattened from all notes)
     * Uses file cache by default (no Redis needed)
     */
    public static function getExistingTags(): array
    {
        return Cache::remember(self::CACHE_KEY_EXISTING_TAGS, self::CACHE_TTL, function () {
            $allTags = [];
            
            self::whereNotNull('tags')
                ->pluck('tags')
                ->each(function ($tags) use (&$allTags) {
                    if (is_array($tags)) {
                        $allTags = array_merge($allTags, $tags);
                    }
                });
            
            // Return unique, sorted tags
            $uniqueTags = array_unique($allTags);
            sort($uniqueTags);
            return array_values($uniqueTags);
        });
    }

    /**
     * Clear the existing tags cache
     * @param string|null $machineType - Optional: 'washing_machine', 'dryer_machine', or 'truck'
     * @param int|null $machineId - Optional: The ID of the machine
     */
    public static function clearTagsCache(?string $machineType = null, ?int $machineId = null): void
    {
        // Always clear global tags cache
        Cache::forget(self::CACHE_KEY_EXISTING_TAGS);
        
        // If machine info provided, clear specific machine cache
        if ($machineType && $machineId) {
            $cacheKey = self::getMachineCacheKey($machineType, $machineId);
            Cache::forget($cacheKey);
        }
    }

    /**
     * Generate cache key for machine-specific tags
     */
    private static function getMachineCacheKey(string $machineType, int $machineId): string
    {
        return "note_tags_{$machineType}_{$machineId}";
    }

    /**
     * Get unique tags for a specific machine (with cache)
     * @param string $machineType - 'washing_machine', 'dryer_machine', or 'truck'
     * @param int $machineId - The ID of the machine
     * @return array
     */
    public static function getTagsForMachine(string $machineType, int $machineId): array
    {
        $cacheKey = self::getMachineCacheKey($machineType, $machineId);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($machineType, $machineId) {
            $column = $machineType . '_id';
            $allTags = [];
            
            self::where($column, $machineId)
                ->whereNotNull('tags')
                ->pluck('tags')
                ->each(function ($tags) use (&$allTags) {
                    if (is_array($tags)) {
                        $allTags = array_merge($allTags, $tags);
                    }
                });
            
            // Return unique, sorted tags
            $uniqueTags = array_unique($allTags);
            sort($uniqueTags);
            return array_values($uniqueTags);
        });
    }

    /**
     * Check if this note has a specific tag
     */
    public function hasTag(string $tag): bool
    {
        return is_array($this->tags) && in_array($tag, $this->tags);
    }

    /**
     * Get color for a specific tag based on hash (for consistent colors)
     */
    public static function getTagColor(string $tag): string
    {
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
        
        $hash = crc32($tag);
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
    protected $fillable = ['message', 'image_url', 'cost', 'tags', 'washing_machine_id', 'dryer_machine_id', 'truck_id'];


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
