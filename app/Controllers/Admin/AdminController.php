<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;
use App\Models\Mensaje;
use PDOException;

/**
 * Base de todos los controladores del panel (/admin/...).
 *
 * Antes de cualquier acción revisa, en este orden:
 *  1. Que haya base de datos (si no, muestra una página 503).
 *  2. Que haya una sesión iniciada (si no, manda a /login).
 *  3. Que el usuario ya haya cambiado la contraseña inicial (si no, solo puede ir a Mi cuenta).
 *  4. Que los formularios POST traigan el token CSRF.
 * Las acciones que modifican datos además llaman a requireAdmin().
 */
abstract class AdminController extends Controller
{
    /** @var string Opción del menú lateral que se marca como activa. */
    protected $section = 'dashboard';

    /** @var bool true solo en Mi cuenta: se puede usar aunque falte cambiar la contraseña. */
    protected $allowedBeforePasswordChange = false;

    public function __construct()
    {
        if (!Database::isAvailable()) {
            $this->view('admin/sin-base-de-datos', [
                'page'    => ['key' => 'error', 'meta_title' => 'Panel no disponible — ValiNix'],
                'noindex' => true,
            ], 503);
            exit;
        }

        if (!Auth::check()) {
            Session::flash('login_error', 'Debes iniciar sesión para entrar al panel.');
            $this->redirect('/login');
        }

        if (Auth::mustChangePassword() && !$this->allowedBeforePasswordChange) {
            $this->back('/admin/cuenta', 'error', 'Antes de usar el panel debes cambiar tu contraseña.');
        }

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !Session::verifyCsrf($_POST['_token'] ?? null)) {
            $this->back('/admin', 'error', 'Tu sesión expiró. Vuelve a intentarlo.');
        }
    }

    /** Solo el rol Administrador puede seguir; los demás ven "Acceso denegado". */
    protected function requireAdmin(): void
    {
        if (Auth::isAdmin()) {
            return;
        }

        $this->render('denegado', ['title' => 'Acceso denegado'], 403);
        exit;
    }

    /** @param array<string, mixed> $data */
    protected function render(string $view, array $data = [], int $status = 200): void
    {
        $isAdmin = Auth::isAdmin();

        $this->view('admin/' . $view, $data + [
            'title'           => 'Panel',
            'user'            => Auth::user(),
            'isAdmin'         => $isAdmin,
            'section'         => $this->section,
            'unread'          => $isAdmin ? (new Mensaje())->contarNoLeidos() : 0,
            'token'           => Session::csrfToken(),
            'flash'           => [
                'status'  => Session::pullFlash('admin_status'),
                'message' => Session::pullFlash('admin_message'),
            ],
            'mustChange'      => Auth::mustChangePassword(),
            'errors'          => [],
        ], $status, 'layouts/admin');
    }

    /** Guarda un mensaje para la siguiente página y redirige. */
    protected function back(string $path, string $status, string $message): void
    {
        Session::flash('admin_status', $status);
        Session::flash('admin_message', $message);
        $this->redirect($path);
    }

    /**
     * Ejecuta una operación de base de datos. Si falla, la registra en el log y
     * devuelve false (el controlador muestra "Error al guardar").
     */
    protected function attempt(callable $operation): bool
    {
        try {
            $operation();
            return true;
        } catch (PDOException $exception) {
            error_log('ValiNix panel: ' . $exception->getMessage());
            return false;
        }
    }

    /** Lee una casilla de verificación (checkbox) del formulario. */
    protected function checkbox(string $key): int
    {
        return isset($_POST[$key]) ? 1 : 0;
    }
}
