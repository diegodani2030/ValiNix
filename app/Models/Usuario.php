<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Tabla usuarios (relacionada con roles).
 * Las contraseñas se guardan con password_hash() y se comparan con password_verify().
 */
final class Usuario extends Model
{
    private const COLUMNAS = 'u.id, u.rol_id, u.nombre, u.nombre_usuario, u.correo, u.activo, u.debe_cambiar_contrasena, u.creado_en, u.actualizado_en, r.nombre AS rol';

    /** Consulta multitabla: usuarios con el nombre de su rol. @return array<int, array<string, mixed>> */
    public function todos(): array
    {
        return $this->all(
            'SELECT ' . self::COLUMNAS . '
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             ORDER BY u.rol_id, u.nombre'
        );
    }

    /** @return array<string, mixed>|null */
    public function buscar(int $id): ?array
    {
        return $this->one(
            'SELECT ' . self::COLUMNAS . '
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.id = :id',
            ['id' => $id]
        );
    }

    /**
     * Para el inicio de sesión: busca por nombre de usuario O por correo (incluye el hash).
     *
     * @return array<string, mixed>|null
     */
    public function buscarParaLogin(string $usuarioOCorreo): ?array
    {
        return $this->one(
            'SELECT u.*, r.nombre AS rol
             FROM usuarios u
             INNER JOIN roles r ON r.id = u.rol_id
             WHERE (u.nombre_usuario = :usuario OR u.correo = :correo) AND u.activo = 1
             LIMIT 1',
            ['usuario' => $usuarioOCorreo, 'correo' => $usuarioOCorreo]
        );
    }

    public function hashDe(int $id): ?string
    {
        $hash = $this->value('SELECT contrasena FROM usuarios WHERE id = :id', ['id' => $id]);

        return $hash === false ? null : (string) $hash;
    }

    /** ¿Ya existe otro usuario con ese valor en la columna? (para validar duplicados) */
    public function existe(string $columna, string $valor, int $exceptoId = 0): bool
    {
        if (!in_array($columna, ['nombre_usuario', 'correo'], true)) {
            return false;
        }

        return (int) $this->value(
            "SELECT COUNT(*) FROM usuarios WHERE {$columna} = :valor AND id <> :id",
            ['valor' => $valor, 'id' => $exceptoId]
        ) > 0;
    }

    public function contarAdministradoresActivos(int $exceptoId = 0): int
    {
        return (int) $this->value(
            'SELECT COUNT(*) FROM usuarios WHERE rol_id = :rol AND activo = 1 AND id <> :id',
            ['rol' => Rol::ADMINISTRADOR, 'id' => $exceptoId]
        );
    }

    public function contar(): int
    {
        return (int) $this->value('SELECT COUNT(*) FROM usuarios');
    }

    /**
     * Crea el usuario. Como la contraseña la eligió el administrador, el
     * usuario deberá cambiarla en su primer ingreso.
     *
     * @param array<string, mixed> $datos nombre, nombre_usuario, correo, rol_id, activo, contrasena (texto plano)
     */
    public function crear(array $datos): int
    {
        $this->run(
            'INSERT INTO usuarios (rol_id, nombre, nombre_usuario, correo, contrasena, activo, debe_cambiar_contrasena)
             VALUES (:rol_id, :nombre, :nombre_usuario, :correo, :contrasena, :activo, 1)',
            [
                'rol_id'         => (int) $datos['rol_id'],
                'nombre'         => $datos['nombre'],
                'nombre_usuario' => $datos['nombre_usuario'],
                'correo'         => $datos['correo'],
                'contrasena'     => password_hash((string) $datos['contrasena'], PASSWORD_DEFAULT),
                'activo'         => (int) $datos['activo'],
            ]
        );

        return $this->lastId();
    }

    /**
     * @param array<string, mixed> $datos Si 'contrasena' viene vacía, no se cambia; si el
     *                                    administrador la cambia, el usuario deberá elegir otra al entrar.
     */
    public function actualizar(int $id, array $datos): void
    {
        $this->run(
            'UPDATE usuarios
             SET rol_id = :rol_id, nombre = :nombre, nombre_usuario = :nombre_usuario, correo = :correo, activo = :activo
             WHERE id = :id',
            [
                'rol_id'         => (int) $datos['rol_id'],
                'nombre'         => $datos['nombre'],
                'nombre_usuario' => $datos['nombre_usuario'],
                'correo'         => $datos['correo'],
                'activo'         => (int) $datos['activo'],
                'id'             => $id,
            ]
        );

        if (($datos['contrasena'] ?? '') !== '') {
            $this->cambiarContrasena($id, (string) $datos['contrasena'], true);
        }
    }

    public function actualizarPerfil(int $id, string $nombre, string $correo): void
    {
        $this->run(
            'UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id',
            ['nombre' => $nombre, 'correo' => $correo, 'id' => $id]
        );
    }

    /** @param bool $debeCambiar true si la puso el administrador (el usuario deberá cambiarla). */
    public function cambiarContrasena(int $id, string $contrasena, bool $debeCambiar = false): void
    {
        $this->run(
            'UPDATE usuarios SET contrasena = :hash, debe_cambiar_contrasena = :debe WHERE id = :id',
            ['hash' => password_hash($contrasena, PASSWORD_DEFAULT), 'debe' => $debeCambiar, 'id' => $id]
        );
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM usuarios WHERE id = :id', ['id' => $id]);
    }
}
