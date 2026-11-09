<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Rol;
use App\Models\Usuario;

/**
 * Inicio y cierre de sesión del panel.
 *
 * - La contraseña se verifica con password_verify() contra el hash guardado.
 * - Al entrar se cambia el id de sesión (evita robo de sesión).
 * - Después de 5 intentos fallidos hay que esperar 5 minutos.
 */
final class Auth
{
    private const MAX_INTENTOS = 5;
    private const BLOQUEO_SEGUNDOS = 300;

    /** Contraseña con la que viene el usuario admin en database/valinix.sql. */
    private const CONTRASENA_INICIAL = 'valinix2026';

    /** @var array<string, mixed>|null|false false = aún no se ha consultado */
    private static $user = false;

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

    /** Solo mira la sesión, sin consultar la base de datos (para el menú público). */
    public static function hasSession(): bool
    {
        return (int) ($_SESSION['auth_id'] ?? 0) > 0;
    }

    /** Segundos que faltan para poder intentar de nuevo (0 = puede intentar). */
    public static function lockedFor(): int
    {
        $until = (int) ($_SESSION['login_bloqueado_hasta'] ?? 0);

        return max(0, $until - time());
    }

    /**
     * Intenta iniciar sesión con usuario o correo + contraseña.
     *
     * @return array<string, mixed>|null El usuario si los datos son correctos.
     */
    public static function attempt(string $usuarioOCorreo, string $contrasena): ?array
    {
        $user = $usuarioOCorreo !== '' ? (new Usuario())->buscarParaLogin($usuarioOCorreo) : null;

        if ($user === null || !password_verify($contrasena, (string) $user['contrasena'])) {
            $intentos = (int) ($_SESSION['login_intentos'] ?? 0) + 1;
            $_SESSION['login_intentos'] = $intentos;
            if ($intentos >= self::MAX_INTENTOS) {
                $_SESSION['login_bloqueado_hasta'] = time() + self::BLOQUEO_SEGUNDOS;
                $_SESSION['login_intentos'] = 0;
            }

            return null;
        }

        session_regenerate_id(true);
        unset($_SESSION['login_intentos'], $_SESSION['login_bloqueado_hasta']);
        $_SESSION['auth_id'] = (int) $user['id'];
        $_SESSION['auth_contrasena_inicial'] = hash_equals(self::CONTRASENA_INICIAL, $contrasena);
        self::$user = false;

        return $user;
    }

    /** ¿Sigue usando la contraseña que viene en el archivo SQL? */
    public static function usesInitialPassword(): bool
    {
        return !empty($_SESSION['auth_contrasena_inicial']);
    }

    public static function forgetInitialPassword(): void
    {
        unset($_SESSION['auth_contrasena_inicial']);
    }

    public static function logout(): void
    {
        unset($_SESSION['auth_id'], $_SESSION['auth_contrasena_inicial']);
        session_regenerate_id(true);
        self::$user = false;
    }
}
