<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controlador base: renderizar vistas y redirigir.
 */
abstract class Controller
{
    /** @param array<string, mixed> $data */
    protected function view(string $view, array $data = [], int $status = 200, ?string $layout = 'layouts/main'): void
    {
        $html = View::render($view, $data, $layout);

        http_response_code($status);
        header('Content-Type: text/html; charset=UTF-8');
        echo $html;
    }

    /** Redirige con 303 (patrón POST → redirección → GET). */
    protected function redirect(string $path, string $fragment = ''): void
    {
        $location = url($path) . ($fragment !== '' ? '#' . $fragment : '');

        header('Location: ' . $location, true, 303);
        exit;
    }

    /** Lee un texto de $_POST, sin espacios a los lados (ignora arreglos enviados a propósito). */
    protected function input(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;

        return is_scalar($value) ? trim(mb_scrub((string) $value, 'UTF-8')) : $default;
    }

    /** Lee un número entero de $_GET o $_POST (0 si no es válido). */
    protected function intParam(string $key): int
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? 0;

        return is_scalar($value) && ctype_digit((string) $value) ? (int) $value : 0;
    }
}
