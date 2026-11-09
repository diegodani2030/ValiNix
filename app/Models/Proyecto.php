<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Tabla proyectos (portafolio). Relacionada con categorias y usuarios.
 * Este es el módulo con CRUD completo: crear, consultar, actualizar y eliminar.
 */
final class Proyecto extends Model
{
    private const CONSULTA = 'SELECT p.id, p.categoria_id, p.usuario_id, p.titulo, p.descripcion, p.enlace, p.color,
                                     p.creado_en, p.actualizado_en,
                                     c.slug AS categoria_slug, c.nombre AS categoria,
                                     u.nombre AS registrado_por
                              FROM proyectos p
                              INNER JOIN categorias c ON c.id = p.categoria_id
                              LEFT JOIN usuarios u ON u.id = p.usuario_id';

    /** Consulta general multitabla (proyecto + categoría + usuario). @return array<int, array<string, mixed>> */
    public function todos(): array
    {
        return $this->all(self::CONSULTA . ' ORDER BY p.id');
    }

    /** Consulta específica: solo los de una categoría. @return array<int, array<string, mixed>> */
    public function porCategoria(int $categoriaId): array
    {
        return $this->all(self::CONSULTA . ' WHERE p.categoria_id = :categoria ORDER BY p.id', ['categoria' => $categoriaId]);
    }

    /** @return array<string, mixed>|null */
    public function buscar(int $id): ?array
    {
        return $this->one(self::CONSULTA . ' WHERE p.id = :id', ['id' => $id]);
    }

    public function contar(): int
    {
        return (int) $this->value('SELECT COUNT(*) FROM proyectos');
    }

    /** @param array<string, mixed> $datos */
    public function crear(array $datos, ?int $usuarioId): int
    {
        $this->run(
            'INSERT INTO proyectos (categoria_id, usuario_id, titulo, descripcion, enlace, color)
             VALUES (:categoria_id, :usuario_id, :titulo, :descripcion, :enlace, :color)',
            $this->parametros($datos) + ['usuario_id' => $usuarioId]
        );

        return $this->lastId();
    }

    /** @param array<string, mixed> $datos */
    public function actualizar(int $id, array $datos): void
    {
        $this->run(
            'UPDATE proyectos
             SET categoria_id = :categoria_id, titulo = :titulo, descripcion = :descripcion, enlace = :enlace, color = :color
             WHERE id = :id',
            $this->parametros($datos) + ['id' => $id]
        );
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM proyectos WHERE id = :id', ['id' => $id]);
    }

    /** @param array<string, mixed> $datos @return array<string, mixed> */
    private function parametros(array $datos): array
    {
        return [
            'categoria_id' => (int) $datos['categoria_id'],
            'titulo'       => $datos['titulo'],
            'descripcion'  => $datos['descripcion'],
            'enlace'       => $datos['enlace'] !== '' ? $datos['enlace'] : '#',
            'color'        => (int) $datos['color'],
        ];
    }
}
