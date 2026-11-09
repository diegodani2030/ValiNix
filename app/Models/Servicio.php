<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tabla servicios. */
final class Servicio extends Model
{
    /** Iconos que se pueden elegir en el formulario: clave del icono => nombre. */
    public const ICONOS = [
        'service-web'      => 'Pantalla (web)',
        'service-catalog'  => 'Cuadrícula (sistemas)',
        'service-shield'   => 'Escudo (seguridad)',
        'service-support'  => 'Chat (soporte)',
        'service-landing'  => 'Casa (landing)',
        'service-business' => 'Documento (empresa)',
        'whatsapp-outline' => 'WhatsApp',
        'service-seo'      => 'Barras (SEO)',
    ];

    /** Servicios visibles en el sitio. @return array<int, array<string, mixed>> */
    public function publicos(): array
    {
        return $this->all('SELECT id, titulo, descripcion, icono FROM servicios WHERE activo = 1 ORDER BY orden, id');
    }

    /** Todos (incluye ocultos) para el panel. @return array<int, array<string, mixed>> */
    public function todos(): array
    {
        return $this->all(
            'SELECT s.*, COUNT(m.id) AS mensajes
             FROM servicios s
             LEFT JOIN mensajes m ON m.servicio_id = s.id
             GROUP BY s.id
             ORDER BY s.orden, s.id'
        );
    }

    /** @return array<string, mixed>|null */
    public function buscar(int $id): ?array
    {
        return $this->one('SELECT * FROM servicios WHERE id = :id', ['id' => $id]);
    }

    public function existeTitulo(string $titulo, int $exceptoId = 0): bool
    {
        return (int) $this->value(
            'SELECT COUNT(*) FROM servicios WHERE titulo = :titulo AND id <> :id',
            ['titulo' => $titulo, 'id' => $exceptoId]
        ) > 0;
    }

    public function contar(): int
    {
        return (int) $this->value('SELECT COUNT(*) FROM servicios WHERE activo = 1');
    }

    /** @param array<string, mixed> $datos */
    public function crear(array $datos): int
    {
        $this->run(
            'INSERT INTO servicios (titulo, descripcion, icono, orden, activo)
             VALUES (:titulo, :descripcion, :icono, :orden, :activo)',
            $this->parametros($datos)
        );

        return $this->lastId();
    }

    /** @param array<string, mixed> $datos */
    public function actualizar(int $id, array $datos): void
    {
        $this->run(
            'UPDATE servicios
             SET titulo = :titulo, descripcion = :descripcion, icono = :icono, orden = :orden, activo = :activo
             WHERE id = :id',
            $this->parametros($datos) + ['id' => $id]
        );
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM servicios WHERE id = :id', ['id' => $id]);
    }

    /** @param array<string, mixed> $datos @return array<string, mixed> */
    private function parametros(array $datos): array
    {
        return [
            'titulo'      => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'icono'       => $datos['icono'],
            'orden'       => (int) $datos['orden'],
            'activo'      => (int) $datos['activo'],
        ];
    }
}
