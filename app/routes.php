<?php
/**
 * Rutas del sitio: URL -> [Controlador, método].
 * Cada sección del menú es una página aparte.
 */

use App\Controllers\Admin\CuentaController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\MensajesController;
use App\Controllers\Admin\ProyectosController;
use App\Controllers\Admin\ServiciosController;
use App\Controllers\Admin\TestimoniosController;
use App\Controllers\Admin\UsuariosController;
use App\Controllers\AuthController;
use App\Controllers\ContactController;
use App\Controllers\PageController;
use App\Core\Router;

$router = new Router();

// --- Sitio público ---------------------------------------------------------
$router->get('/', [PageController::class, 'home']);
$router->get('/nosotros', [PageController::class, 'about']);
$router->get('/servicios', [PageController::class, 'services']);
$router->get('/proceso', [PageController::class, 'process']);
$router->get('/proyectos', [PageController::class, 'projects']);
$router->get('/precios', [PageController::class, 'pricing']);
$router->get('/preguntas-frecuentes', [PageController::class, 'faq']);
$router->get('/manual', [PageController::class, 'manual']);

$router->get('/contacto', [ContactController::class, 'show']);
$router->post('/contacto', [ContactController::class, 'store']);

// Ruta antigua del formulario, por si alguien tiene la página vieja en caché.
$router->post('/includes/process_form.php', [ContactController::class, 'store']);

// --- Inicio y cierre de sesión ---------------------------------------------
$router->get('/login', [AuthController::class, 'form']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// --- Panel administrativo --------------------------------------------------
$router->get('/admin', [DashboardController::class, 'index']);

// Módulos con CRUD: listar, formulario nuevo, guardar, formulario editar, actualizar, eliminar.
$modulos = [
    'proyectos'   => ProyectosController::class,
    'servicios'   => ServiciosController::class,
    'testimonios' => TestimoniosController::class,
    'usuarios'    => UsuariosController::class,
];
foreach ($modulos as $modulo => $controlador) {
    $router->get("/admin/{$modulo}", [$controlador, 'index']);
    $router->get("/admin/{$modulo}/nuevo", [$controlador, 'crear']);
    $router->post("/admin/{$modulo}/guardar", [$controlador, 'guardar']);
    $router->get("/admin/{$modulo}/editar", [$controlador, 'editar']);
    $router->post("/admin/{$modulo}/actualizar", [$controlador, 'actualizar']);
    $router->post("/admin/{$modulo}/eliminar", [$controlador, 'eliminar']);
}

$router->get('/admin/mensajes', [MensajesController::class, 'index']);
$router->get('/admin/mensajes/ver', [MensajesController::class, 'ver']);
$router->post('/admin/mensajes/estado', [MensajesController::class, 'estado']);
$router->post('/admin/mensajes/eliminar', [MensajesController::class, 'eliminar']);

$router->get('/admin/cuenta', [CuentaController::class, 'ver']);
$router->post('/admin/cuenta', [CuentaController::class, 'actualizar']);

return $router;
