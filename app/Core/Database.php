<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Conexión única a MySQL con PDO.
 *
 * - Se conecta solo cuando alguien la pide (la primera consulta).
 * - Usa consultas preparadas reales y lanza excepciones si algo falla.
 * - Si no hay datos de conexión o el servidor no responde, lanza
 *   DatabaseException para que el sitio muestre el contenido de respaldo.
 */
final class Database
{
    /** @var PDO|null */
    private static $pdo = null;

    /** @var DatabaseException|null Error de la conexión, para no reintentar en la misma petición. */
    private static $error = null;

    public static function isConfigured(): bool
    {
        return (string) config('db.host', '') !== '' && (string) config('db.name', '') !== '';
    }

    /** @throws DatabaseException */
    public static function connection(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }
        if (self::$error !== null) {
            throw self::$error;
        }
        if (!self::isConfigured()) {
            throw self::$error = new DatabaseException('La base de datos no está configurada.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            (string) config('db.host'),
            (int) config('db.port', 3306),
            (string) config('db.name')
        );

        try {
            self::$pdo = new PDO($dsn, (string) config('db.user'), (string) config('db.password'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 5,
            ]);
        } catch (PDOException $exception) {
            // El mensaje real (con host y usuario) solo va al log, nunca a la pantalla.
            error_log('ValiNix: no se pudo conectar a MySQL: ' . $exception->getMessage());
            throw self::$error = new DatabaseException('No se pudo conectar a la base de datos.', 0, $exception);
        }

        return self::$pdo;
    }

    /** true si hay conexión (intenta conectarse si hace falta). */
    public static function isAvailable(): bool
    {
        try {
            self::connection();
            return true;
        } catch (DatabaseException $exception) {
            return false;
        }
    }
}
