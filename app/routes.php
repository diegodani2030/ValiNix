<?php
/**
 * Rutas del sitio: URL -> [Controlador, método].
 */

use App\Controllers\ContactController;
use App\Controllers\HomeController;
use App\Core\Router;

$router = new Router();

$router->get('/', [HomeController::class, 'index']);

$router->post('/contacto', [ContactController::class, 'store']);
$router->get('/contacto', [ContactController::class, 'show']);

// Ruta antigua del formulario, por si alguien tiene la página vieja en caché.
$router->post('/includes/process_form.php', [ContactController::class, 'store']);

return $router;
