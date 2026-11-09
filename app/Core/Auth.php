<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\IntentoLogin;
use App\Models\Rol;
use App\Models\Usuario;

/**
 * Inicio y cierre de sesión del panel.
 *
 * - La contraseña se verifica con password_verify() contra el hash guardado.
 * - Si el usuario no existe también se ejecuta password_verify() (con un hash
 *   de relleno) para que la respuesta tarde lo mismo y no revele qué usuarios existen.
 * - Los intentos se guardan en la tabla intentos_login: 5 fallos con el mismo
 *   usuario desde la misma IP, 30 desde la misma IP con cualquier usuario, o
 *   50 contra el mismo usuario desde cualquier IP, bloquean 15 minutos. Cada intento se anota ANTES de revisar la contraseña,
 *   así varios intentos enviados al mismo tiempo también cuentan.
 * - Al entrar se cambia el id de sesión (evita robo de sesión).
 */
final class Auth
{
    private const MAX_POR_USUARIO = 5;
    private const MAX_POR_IP = 30;
    private const MAX_POR_CUENTA = 50; // alto, para que no sirva para bloquear al admin a propósito
    private const VENTANA_MINUTOS = 15;

    /** Hash bcrypt de relleno (de una contraseña aleatoria que nadie conoce). */
    private const HASH_RELLENO = '$2y$10$f5VYo4aqDEVhFrFMnQP6qO/HT5GDSHD5xUoXr02L6NGAgNutDSkWG';

    /** @var array<string, mixed>|null|false false = aún no se ha consultado */
    private static $user = false;

    /** @var bool true si el último attempt() se rechazó por bloqueo. */
    private static $locked = false;

    /**
     * Usuario con sesión iniciada (se vuelve a leer de la base de datos para
     * saber si sigue activo y con qué rol). null si no hay sesión.
     *
     * @return array<string, mixed>|null
     */
    public static function user(): ?array
    {
        if (self::$user !== false) {
            return self::$user;
        }

        $id = (int) ($_SESSION['auth_id'] ?? 0);
        self::$user = null;

        if ($id > 0) {
            $user = (new Usuario())->buscar($id);
            if ($user !== null && (int) $user['activo'] === 1) {
                self::$user = $user;
            } else {
                unset($_SESSION['auth_id']);
            }
        }

        return self::$user;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        $user = self::user();

        return $user !== null && (int) $user['rol_id'] === Rol::ADMINISTRADOR;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user !== null ? (int) $user['id'] : null;
    }

    /** ¿El usuario debe elegir una contraseña nueva antes de usar el panel? */
    public static function mustChangePassword(): bool
    {
        $user = self::user();

        return $user !== null && (int) ($user['debe_cambiar_contrasena'] ?? 0) === 1;
    }

    /** Solo mira la sesión, sin consultar la base de datos (para el menú público). */
    public static function hasSession(): bool
    {
        return (int) ($_SESSION['auth_id'] ?? 0) > 0;
    }

    /** ¿Ese usuario/correo o esta IP están bloqueados por demasiados intentos fallidos? */
    public static function isLocked(string $usuarioOCorreo): bool
    {
        $intentos = new IntentoLogin();

        return $intentos->fallosPorClaveEIp(self::clave($usuarioOCorreo), self::ip(), self::VENTANA_MINUTOS) >= self::MAX_POR_USUARIO
            || $intentos->fallosPorIp(self::ip(), self::VENTANA_MINUTOS) >= self::MAX_POR_IP
            || $intentos->fallosPorClave(self::clave($usuarioOCorreo), self::VENTANA_MINUTOS) >= self::MAX_POR_CUENTA;
    }

    /** ¿El último intento se rechazó por bloqueo (y no por datos incorrectos)? */
    public static function wasLocked(): bool
    {
        return self::$locked;
    }

    public static function lockMinutes(): int
    {
        return self::VENTANA_MINUTOS;
    }

    /**
     * Intenta iniciar sesión con usuario o correo + contraseña.
     *
     * @return array<string, mixed>|null El usuario si los datos son correctos.
     */
    public static function attempt(string $usuarioOCorreo, string $contrasena): ?array
    {
        $intentos = new IntentoLogin();
        $clave    = self::clave($usuarioOCorreo);
        $ip       = self::ip();
        self::$locked = false;

        // 1. Se anota el intento antes de revisar nada y se cuenta incluyéndolo.
        $intentos->registrarFallo($clave, $ip);
        if ($intentos->fallosPorClaveEIp($clave, $ip, self::VENTANA_MINUTOS) > self::MAX_POR_USUARIO
            || $intentos->fallosPorIp($ip, self::VENTANA_MINUTOS) > self::MAX_POR_IP
            || $intentos->fallosPorClave($clave, self::VENTANA_MINUTOS) > self::MAX_POR_CUENTA) {
            self::$locked = true;
            return null;
        }

        $user = (new Usuario())->buscarParaLogin($usuarioOCorreo);

        // Siempre se verifica un hash, exista o no el usuario (mismo tiempo de respuesta).
        $valida = password_verify($contrasena, $user !== null ? (string) $user['contrasena'] : self::HASH_RELLENO);

        // 2. Si falla, el intento anotado queda como fallo; si acierta, se borran sus fallos.
        if ($user === null || !$valida) {
            $intentos->purgarViejos();
            return null;
        }

        $intentos->limpiar($clave, $ip);
        session_regenerate_id(true);
        $_SESSION['auth_id'] = (int) $user['id'];
        self::$user = false;

        return $user;
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_id']);
        session_regenerate_id(true);
        self::$user = false;
    }

    private static function clave(string $usuarioOCorreo): string
    {
        return mb_substr(mb_strtolower(trim($usuarioOCorreo)), 0, 150);
    }

    private static function ip(): string
    {
        return mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
    }
}
