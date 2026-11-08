<?php
/**
 * Front controller de ValiNix.
 *
 * Todas las peticiones (excepto archivos estáticos como css/, js/ y assets/)
 * llegan aquí gracias al .htaccess. Este archivo solo arranca la aplicación
 * y le pasa la petición al router; la lógica vive en app/.
 */

declare(strict_types=1);

// Servidor embebido de PHP (`php -S localhost:8000 index.php`):
// deja que sirva directamente los archivos estáticos que existan.
if (PHP_SAPI === 'cli-server') {
    $requestPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $isPrivate   = (bool) preg_match('#^/(app|config)(/|$)#', $requestPath);
    if (!$isPrivate && $requestPath !== '/' && is_file(__DIR__ . $requestPath)) {
        return false;
    }
}

define('BASE_PATH', __DIR__);

require BASE_PATH . '/app/bootstrap.php';

/** @var \App\Core\Router $router */
$router = require BASE_PATH . '/app/routes.php';

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
