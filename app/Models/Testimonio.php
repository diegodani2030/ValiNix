<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tabla testimonios. */
final class Testimonio extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function todos(): array
    {
        return $this->all('SELECT * FROM testimonios ORDER BY orden, id');
    }

    /** @return array<string, mixed>|null */
    public function buscar(int $id): ?array
    {
        return $this->one('SELECT * FROM testimonios WHERE id = :id', ['id' => $id]);
    }

    /** @param array<string, mixed> $datos */
    public function crear(array $datos): int
    {
        $this->run(
            'INSERT INTO testimonios (autor, empresa, calificacion, comentario, orden)
             VALUES (:autor, :empresa, :calificacion, :comentario, :orden)',
            $this->parametros($datos)
        );

        return $this->lastId();
    }

    /** @param array<string, mixed> $datos */
    public function actualizar(int $id, array $datos): void
    {
        $this->run(
            'UPDATE testimonios
             SET autor = :autor, empresa = :empresa, calificacion = :calificacion, comentario = :comentario, orden = :orden
             WHERE id = :id',
            $this->parametros($datos) + ['id' => $id]
        );
    }

    public function eliminar(int $id): void
    {
        $this->run('DELETE FROM testimonios WHERE id = :id', ['id' => $id]);
    }

    /** @param array<string, mixed> $datos @return array<string, mixed> */
    private function parametros(array $datos): array
    {
        return [
            'autor'        => $datos['autor'],
            'empresa'      => $datos['empresa'],
            'calificacion' => (int) $datos['calificacion'],
            'comentario'   => $datos['comentario'],
            'orden'        => (int) $datos['orden'],
        ];
    }
}
