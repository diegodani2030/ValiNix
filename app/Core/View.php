<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Renderiza las plantillas de app/Views dentro de un layout.
 */
final class View
{
    /** @var array<string, mixed> Variables disponibles en todas las vistas (ej. el menú). */
    private static $shared = [];

    /** @param mixed $value */
    public static function share(string $key, $value): void
    {
        self::$shared[$key] = $value;
    }

    /**
     * @param array<string, mixed> $data Variables disponibles en la vista.
     * @param string|null $layout Layout que envuelve la vista (null = sin layout).
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/main'): string
    {
        $data    = array_merge(self::$shared, $data);
        $content = self::capture($view, $data);

        if ($layout === null) {
            return $content;
        }

        return self::capture($layout, array_merge($data, ['content' => $content]));
    }

    /** @param array<string, mixed> $data */
    private static function capture(string $view, array $data): string
    {
        $__file = APP_PATH . '/Views/' . $view . '.php';

        if (!is_file($__file)) {
            throw new RuntimeException('Vista no encontrada: ' . $view);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        try {
            include $__file;
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        return (string) ob_get_clean();
    }
}
