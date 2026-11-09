<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Session;

/**
 * Inicio y cierre de sesión (botón "Iniciar sesión" del menú).
 */
final class AuthController extends Controller
{
    /** GET /login */
    public function form(): void
    {
        if (Database::isAvailable() && Auth::check()) {
            $this->redirect('/admin');
        }

        $this->render([
            'usuario' => (string) Session::pullFlash('login_usuario', ''),
            'error'   => Session::pullFlash('login_error'),
        ]);
    }

    /** POST /login */
    public function login(): void
    {
        $usuario    = mb_substr($this->input('usuario'), 0, 150);
        $contrasena = is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '';

        if (!Session::verifyCsrf($_POST['_token'] ?? null)) {
            $this->fail($usuario, 'Tu sesión expiró. Intenta de nuevo.');
        }
        if ($usuario === '' || $contrasena === '') {
            $this->fail($usuario, 'Campo obligatorio: escribe tu usuario o correo y tu contraseña.');
        }
        if (!Database::isAvailable()) {
            $this->fail($usuario, 'El sistema no está disponible en este momento (sin conexión a la base de datos).');
        }

        $espera = Auth::lockedFor();
        if ($espera > 0) {
            $this->fail($usuario, 'Demasiados intentos fallidos. Espera ' . (int) ceil($espera / 60) . ' minuto(s) e intenta de nuevo.');
        }

        $user = Auth::attempt($usuario, $contrasena);
        if ($user === null) {
            $this->fail($usuario, 'Usuario o contraseña incorrectos.');
        }

        Session::flash('admin_status', 'success');
        Session::flash('admin_message', 'Bienvenido, ' . $user['nombre'] . '.');
        $this->redirect('/admin');
    }

    /** POST /logout */
    public function logout(): void
    {
        if (Session::verifyCsrf($_POST['_token'] ?? null)) {
            Auth::logout();
            Session::flash('login_ok', 'Cerraste sesión correctamente.');
        }

        $this->redirect('/login');
    }

    private function fail(string $usuario, string $message): void
    {
        Session::flash('login_usuario', $usuario);
        Session::flash('login_error', $message);
        $this->redirect('/login');
    }

    /** @param array<string, mixed> $data */
    private function render(array $data): void
    {
        $this->view('pages/login', $data + [
            'page' => [
                'key'              => 'login',
                'nav'              => 'Iniciar sesión',
                'eyebrow'          => 'Panel ValiNix',
                'heading'          => 'Iniciar',
                'heading_accent'   => 'sesión',
                'lead'             => 'Ingresa con tu cuenta de administrador o de usuario registrado.',
                'meta_title'       => 'Iniciar sesión — ' . config('app.name', 'ValiNix'),
                'meta_description' => 'Acceso al panel administrativo de ValiNix.',
            ],
            'noindex'      => true,
            'token'        => Session::csrfToken(),
            'ok'           => Session::pullFlash('login_ok'),
            'dbConfigured' => Database::isConfigured(),
        ]);
    }
}
