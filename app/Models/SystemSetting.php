<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key', 'value', 'type', 'description'
    ];

    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? self::castValue($setting->value, $setting->type) : $default;
    }

    public static function set(string $key, $value, string $type = 'string', string $description = ''): void
    {
        self::updateOrCreate(['key' => $key], [
            'value' => $value,
            'type' => $type,
            'description' => $description
        ]);
    }

    private static function castValue($value, $type)
    {
        return match ($type) {
            'boolean' => $value === 'true' || $value === 1 || $value === true,
            'number' => (float)$value,
            'json' => json_decode($value, true),
            default => $value
        };
    }
}
