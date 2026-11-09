<?php
/**
 * Rutas del sitio: URL -> [Controlador, método].
 * Cada sección del menú es una página aparte.
 */

use App\Controllers\ContactController;
use App\Controllers\PageController;
use App\Core\Router;

$router = new Router();

$router->get('/', [PageController::class, 'home']);
$router->get('/servicios', [PageController::class, 'services']);
$router->get('/proceso', [PageController::class, 'process']);
$router->get('/proyectos', [PageController::class, 'projects']);
$router->get('/precios', [PageController::class, 'pricing']);
$router->get('/preguntas-frecuentes', [PageController::class, 'faq']);

$router->get('/contacto', [ContactController::class, 'show']);
$router->post('/contacto', [ContactController::class, 'store']);

// Ruta antigua del formulario, por si alguien tiene la página vieja en caché.
$router->post('/includes/process_form.php', [ContactController::class, 'store']);

return $router;
