<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Controlador base: renderizar vistas y redirigir.
 */
abstract class Controller
{
    /** @param array<string, mixed> $data */
    protected function view(string $view, array $data = [], int $status = 200): void
    {
        $html = View::render($view, $data);

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
}
