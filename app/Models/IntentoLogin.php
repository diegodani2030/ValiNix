<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Tabla intentos_login: intentos fallidos de inicio de sesión.
 * Se cuentan por usuario/correo y por IP en los últimos minutos, desde el
 * servidor (borrar la cookie de sesión no reinicia el contador).
 */
final class IntentoLogin extends Model
{
    public function registrarFallo(string $clave, string $ip): void
    {
        $this->run('INSERT INTO intentos_login (clave, ip) VALUES (:clave, :ip)', ['clave' => $clave, 'ip' => $ip]);
    }

    /**
     * Fallos recientes para ese usuario/correo DESDE ESA IP. Así, alguien desde
     * otro equipo no puede dejar bloqueada la cuenta del administrador.
     */
    public function fallosPorClaveEIp(string $clave, string $ip, int $minutos): int
    {
        return (int) $this->value(
            'SELECT COUNT(*) FROM intentos_login WHERE clave = :clave AND ip = :ip AND creado_en > (NOW() - INTERVAL :minutos MINUTE)',
            ['clave' => $clave, 'ip' => $ip, 'minutos' => $minutos]
        );
    }

    /** Fallos recientes desde esa IP (con cualquier usuario). */
    public function fallosPorIp(string $ip, int $minutos): int
    {
        return (int) $this->value(
            'SELECT COUNT(*) FROM intentos_login WHERE ip = :ip AND creado_en > (NOW() - INTERVAL :minutos MINUTE)',
            ['ip' => $ip, 'minutos' => $minutos]
        );
    }

    /** Al entrar bien se borran los fallos de ese usuario desde esa IP. */
    public function limpiar(string $clave, string $ip): void
    {
        $this->run('DELETE FROM intentos_login WHERE clave = :clave AND ip = :ip', ['clave' => $clave, 'ip' => $ip]);
    }

    /** Borra los registros de más de un día para que la tabla no crezca. */
    public function purgarViejos(): void
    {
        $this->run('DELETE FROM intentos_login WHERE creado_en < (NOW() - INTERVAL 1 DAY)');
    }
}
