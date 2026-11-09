<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tabla mensajes (formulario de contacto). Relacionada con servicios y planes. */
final class Mensaje extends Model
{
    private const CONSULTA = 'SELECT m.*, s.titulo AS servicio, pl.nombre AS plan
                              FROM mensajes m
                              LEFT JOIN servicios s ON s.id = m.servicio_id
                              LEFT JOIN planes pl ON pl.id = m.plan_id';

    /** @param array<string, mixed> $datos */
    public function crear(array $datos): int
    {
        $this->run(
            'INSERT INTO mensajes (servicio_id, plan_id, nombre, empresa, correo, telefono, asunto, mensaje)
             VALUES (:servicio_id, :plan_id, :nombre, :empresa, :correo, :telefono, :asunto, :mensaje)',
            [
                'servicio_id' => $datos['servicio_id'],
                'plan_id'     => $datos['plan_id'],
                'nombre'      => $datos['nombre'],
                'empresa'     => $datos['empresa'] !== '' ? $datos['empresa'] : null,
                'correo'      => $datos['correo'],
                'telefono'    => $datos['telefono'],
                'asunto'      => $datos['asunto'],
                'mensaje'     => $datos['mensaje'],
            ]
        );

        return $this->lastId();
    }

    /**
     * Consulta multitabla con filtros opcionales.
     *
     * @param string $buscar Texto a buscar en nombre, correo o asunto ('' = todos).
     * @param string $estado 'nuevos', 'leidos' o '' (todos).
     * @return array<int, array<string, mixed>>
     */
    public function listar(string $buscar = '', string $estado = '', int $limite = 100): array
    {
        $condiciones = [];
        $parametros  = [];

        if ($buscar !== '') {
            $condiciones[] = '(m.nombre LIKE :b1 OR m.correo LIKE :b2 OR m.asunto LIKE :b3)';
            $patron = '%' . addcslashes($buscar, '%_\\') . '%';
            $parametros += ['b1' => $patron, 'b2' => $patron, 'b3' => $patron];
        }
        if ($estado === 'nuevos' || $estado === 'leidos') {
            $condiciones[] = 'm.leido = :leido';
            $parametros['leido'] = $estado === 'leidos' ? 1 : 0;
        }

        $where = $condiciones !== [] ? ' WHERE ' . implode(' AND ', $condiciones) : '';
        $parametros['limite'] = $limite;

        return $this->all(self::CONSULTA . $where . ' ORDER BY m.creado_en DESC, m.id DESC LIMIT :limite', $parametros);
    }

    /** @return array<string, mixed>|null */
    public function buscar(int $id): ?array
    {
        return $this->one(self::CONSULTA . ' WHERE m.id = :id', ['id' => $id]);
    }

    public function marcarLeido(int $id, bool $leido = true): void
    {
        $this->run('UPDATE mensajes SET leido = :leido WHERE id = :id', ['leido' => $leido, 'id' => $id]);
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM mensajes WHERE id = :id', ['id' => $id]);
    }

    public function contar(): int
    {
        return (int) $this->value('SELECT COUNT(*) FROM mensajes');
    }

    public function contarNoLeidos(): int
    {
        return (int) $this->value('SELECT COUNT(*) FROM mensajes WHERE leido = 0');
    }

    /** Consulta multitabla con agrupación: mensajes por servicio. @return array<int, array<string, mixed>> */
    public function porServicio(): array
    {
        return $this->all(
            "SELECT COALESCE(s.titulo, 'Sin servicio') AS servicio, COUNT(m.id) AS total
             FROM mensajes m
             LEFT JOIN servicios s ON s.id = m.servicio_id
             GROUP BY s.id, s.titulo
             ORDER BY total DESC, servicio"
        );
    }
}
