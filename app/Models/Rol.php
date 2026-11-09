<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/** Tabla roles: tipos de usuario del panel. */
final class Rol extends Model
{
    public const ADMINISTRADOR = 1;
    public const USUARIO_REGISTRADO = 2;

    /** @return array<int, array<string, mixed>> */
    public function todos(): array
    {
        return $this->all('SELECT id, nombre, descripcion FROM roles ORDER BY id');
    }

    /** Opciones para el <select> del formulario: id => nombre. @return array<int, string> */
    public function opciones(): array
    {
        $opciones = [];
        foreach ($this->todos() as $rol) {
            $opciones[(int) $rol['id']] = (string) $rol['nombre'];
        }

        return $opciones;
    }
}
