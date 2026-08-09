<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            if (!$setting) return $default;

            return match ($setting->type) {
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                default   => $setting->value,
            };
        });
    }
}

if (!function_exists('clear_setting_cache')) {
    function clear_setting_cache(?string $key = null): void
    {
        if ($key) {
            Cache::forget("setting.{$key}");
        } else {
            // Clear all setting caches
            $settings = Setting::pluck('key');
            foreach ($settings as $settingKey) {
                Cache::forget("setting.{$settingKey}");
            }
        }
    }
}