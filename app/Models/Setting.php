<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = "setting_{$key}";

        return Cache::remember($cacheKey, 60 * 60, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            $value = $setting->value;

            // Try to decode JSON if it's a JSON string
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }

            return $value;
        });
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set(string $key, $value)
    {
        // If value is array or object, encode as JSON
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear the cache for this key
        Cache::forget("setting_{$key}");
    }

    /**
     * Get all settings as key-value pairs.
     *
     * @return array
     */
    public static function getAllSettings()
    {
        return Cache::remember('all_settings', 60 * 60, function () {
            $settings = self::all()->pluck('value', 'key')->toArray();

            // Try to decode JSON values
            foreach ($settings as $key => $value) {
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $settings[$key] = $decoded;
                    }
                }
            }

            return $settings;
        });
    }

    /**
     * Clear the settings cache.
     *
     * @return void
     */
    public static function clearCache()
    {
        Cache::forget('all_settings');

        // Clear individual setting caches
        foreach (self::all() as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
    }
}
