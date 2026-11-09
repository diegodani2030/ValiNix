<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tablas planes y plan_caracteristicas (1:N). */
final class Plan extends Model
{
    /**
     * Consulta multitabla: cada plan con su lista de características.
     * El LEFT JOIN trae una fila por característica y aquí se agrupan por plan.
     *
     * @return array<int, array<string, mixed>>
     */
    public function conCaracteristicas(): array
    {
        $filas = $this->all(
            'SELECT pl.id, pl.clave, pl.nombre, pl.descripcion, pl.precio, pl.destacado, pl.etiqueta,
                    pc.texto AS caracteristica
             FROM planes pl
             LEFT JOIN plan_caracteristicas pc ON pc.plan_id = pl.id
             ORDER BY pl.orden, pl.id, pc.orden, pc.id'
        );

        $planes = [];
        foreach ($filas as $fila) {
            $id = (int) $fila['id'];
            if (!isset($planes[$id])) {
                $planes[$id] = [
                    'id'          => $id,
                    'clave'       => $fila['clave'],
                    'nombre'      => $fila['nombre'],
                    'descripcion' => $fila['descripcion'],
                    'precio'      => $fila['precio'],
                    'destacado'   => (bool) $fila['destacado'],
                    'etiqueta'    => $fila['etiqueta'],
                    'caracteristicas' => [],
                ];
            }
            if ($fila['caracteristica'] !== null) {
                $planes[$id]['caracteristicas'][] = $fila['caracteristica'];
            }
        }

        return array_values($planes);
    }

    /** @return array<string, mixed>|null */
    public function buscarPorClave(string $clave): ?array
    {
        return $this->one('SELECT id, clave, nombre FROM planes WHERE clave = :clave', ['clave' => $clave]);
    }
}
