<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'value',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get a setting value by slug with caching
     *
     * @param string $slug
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($slug, $default = null)
    {
        return Cache::remember("setting_{$slug}", 3600, function () use ($slug, $default) {
            $setting = static::where('slug', $slug)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by slug
     *
     * @param string $slug
     * @param mixed $value
     * @param string $name
     * @return static
     */
    public static function setValue($slug, $value, $name = null)
    {
        $setting = static::updateOrCreate(
            ['slug' => $slug],
            [
                'value' => $value,
                'name' => $name ?: ucfirst(str_replace(['_', '-'], ' ', $slug)),
            ]
        );

        // Clear cache
        Cache::forget("setting_{$slug}");

        return $setting;
    }

    /**
     * Get multiple settings by slugs
     *
     * @param array $slugs
     * @return \Illuminate\Support\Collection
     */
    public static function getMultiple(array $slugs)
    {
        return static::whereIn('slug', $slugs)->get()->keyBy('slug');
    }

    /**
     * Get all Nomba payment settings
     *
     * @return array
     */
    public static function getNombaSettings()
    {
        $slugs = [
            'nombaAccountID',
            'nombaClientID',
            'nombaPrivatekey',
            'nombaWebhookSecret'
        ];

        $settings = static::getMultiple($slugs);

        return [
            'account_id' => $settings->get('nombaAccountID')?->value,
            'client_id' => $settings->get('nombaClientID')?->value,
            'private_key' => $settings->get('nombaPrivatekey')?->value,
            'webhook_secret' => $settings->get('nombaWebhookSecret')?->value,
        ];
    }

    /**
     * Check if all required Nomba settings are configured
     *
     * @return bool
     */
    public static function isNombaConfigured()
    {
        $settings = static::getNombaSettings();

        return !empty($settings['account_id']) &&
               !empty($settings['client_id']) &&
               !empty($settings['private_key']);
    }

    /**
     * Get all application settings
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getAppSettings()
    {
        return Cache::remember('app_settings', 3600, function () {
            return static::all()->keyBy('slug');
        });
    }

    /**
     * Clear all settings cache
     *
     * @return void
     */
    public static function clearCache()
    {
        $settings = static::all();

        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->slug}");
        }

        Cache::forget('app_settings');
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("setting_{$setting->slug}");
            Cache::forget('app_settings');
        });

        static::deleted(function ($setting) {
            Cache::forget("setting_{$setting->slug}");
            Cache::forget('app_settings');
        });
    }

    /**
     * Scope to search settings by name or slug
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%")
              ->orWhere('value', 'like', "%{$search}%");
        });
    }

    /**
     * Get the setting's display name
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: ucfirst(str_replace(['_', '-'], ' ', $this->slug));
    }

    /**
     * Check if the setting is a sensitive value (like API keys)
     *
     * @return bool
     */
    public function getIsSensitiveAttribute()
    {
        $sensitiveKeywords = [
            'password', 'secret', 'key', 'token', 'private',
            'credential', 'api_key', 'privatekey'
        ];

        $slug = strtolower($this->slug);

        foreach ($sensitiveKeywords as $keyword) {
            if (str_contains($slug, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get masked value for sensitive settings
     *
     * @return string
     */
    public function getMaskedValueAttribute()
    {
        if ($this->is_sensitive && $this->value) {
            return '****' . substr($this->value, -4);
        }

        return $this->value;
    }
}
