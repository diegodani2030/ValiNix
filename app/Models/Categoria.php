<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tabla categorias (tipos de proyecto). */
final class Categoria extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function todas(): array
    {
        return $this->all('SELECT id, slug, nombre FROM categorias ORDER BY id');
    }

    /** Opciones para el <select>: id => nombre. @return array<int, string> */
    public function opciones(): array
    {
        $opciones = [];
        foreach ($this->todas() as $categoria) {
            $opciones[(int) $categoria['id']] = (string) $categoria['nombre'];
        }

        return $opciones;
    }

    /**
     * Consulta multitabla con agrupación: categorías que tienen proyectos y cuántos.
     * Se usa para los filtros de /proyectos y el resumen del panel.
     *
     * @return array<int, array<string, mixed>>
     */
    public function conProyectos(): array
    {
        return $this->all(
            'SELECT c.id, c.slug, c.nombre, COUNT(p.id) AS total
             FROM categorias c
             INNER JOIN proyectos p ON p.categoria_id = c.id
             GROUP BY c.id, c.slug, c.nombre
             HAVING COUNT(p.id) > 0
             ORDER BY c.id'
        );
    }
}
