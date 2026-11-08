<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

/**
 * Páginas de error.
 */
final class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->errorPage(404);
    }

    /** La ruta existe pero con otro método (ej. GET a una ruta solo POST). @param string[] $allowed */
    public function methodNotAllowed(array $allowed): void
    {
        header('Allow: ' . implode(', ', $allowed));
        $this->errorPage(405);
    }

    private function errorPage(int $status): void
    {
        $this->view('errors/404', [
            'pageTitle' => 'Página no encontrada — ' . config('app.name', 'ValiNix'),
            'navBase'   => url('/'),
            'noindex'   => true,
        ], $status);
    }
}
