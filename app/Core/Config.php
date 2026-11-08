<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Guarda la configuración cargada desde config/config.php.
 */
final class Config
{
    /** @var array<string, mixed> */
    private static $items = [];

    public static function load(string $file): void
    {
        $items = require $file;
        self::$items = is_array($items) ? $items : [];
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $value = self::$items;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
