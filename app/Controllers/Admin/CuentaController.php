<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Validator;
use App\Models\Usuario;

/** "Mi cuenta": cada usuario cambia su nombre, correo y contraseña. */
final class CuentaController extends AdminController
{
    protected $section = 'cuenta';

    /** GET /admin/cuenta */
    public function ver(): void
    {
        $this->formulario((array) Auth::user());
    }

    /** POST /admin/cuenta */
    public function actualizar(): void
    {
        $id     = (int) Auth::id();
        $modelo = new Usuario();

        $datos = [
            'nombre'     => $this->input('nombre'),
            'correo'     => mb_strtolower($this->input('correo')),
            'actual'     => is_string($_POST['actual'] ?? null) ? $_POST['actual'] : '',
            'contrasena' => is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '',
            'confirmar'  => is_string($_POST['confirmar'] ?? null) ? $_POST['confirmar'] : '',
        ];

        $v = new Validator($datos, [
            'nombre'     => 'Nombre completo',
            'correo'     => 'Correo electrónico',
            'actual'     => 'Contraseña actual',
            'contrasena' => 'Nueva contraseña',
        ]);
        $v->required('nombre', 'correo')
          ->min('nombre', 3)->max('nombre', 100)
          ->max('correo', 150)->email('correo')
          ->check($datos['correo'] !== '' && $modelo->existe('correo', $datos['correo'], $id), 'correo', 'Ese correo ya está registrado.');

        $cambiaContrasena = $datos['contrasena'] !== '';
        if ($cambiaContrasena) {
            $v->required('actual')
              ->check($datos['actual'] !== '' && !password_verify($datos['actual'], (string) $modelo->hashDe($id)), 'actual', 'La contraseña actual no es correcta.')
              ->min('contrasena', 8)->max('contrasena', 72)
              ->check($datos['contrasena'] === $datos['actual'], 'contrasena', 'La nueva contraseña debe ser diferente a la actual.')
              ->same('confirmar', 'contrasena');
        }

        if ($v->fails()) {
            $this->formulario(['nombre' => $datos['nombre'], 'correo' => $datos['correo']] + (array) Auth::user(), $v->errors(), 422);
            return;
        }

        if (!$this->attempt(function () use ($modelo, $id, $datos, $cambiaContrasena): void {
            $modelo->actualizarPerfil($id, $datos['nombre'], $datos['correo']);
            if ($cambiaContrasena) {
                $modelo->cambiarContrasena($id, $datos['contrasena']);
            }
        })) {
            $this->back('/admin/cuenta', 'error', 'Error al guardar: no se pudieron guardar los cambios.');
        }

        if ($cambiaContrasena) {
            Auth::forgetInitialPassword();
        }

        $this->back('/admin/cuenta', 'success', 'Registro actualizado correctamente.');
    }

    /**
     * @param array<string, mixed>  $usuario
     * @param array<string, string> $errores
     */
    private function formulario(array $usuario, array $errores = [], int $status = 200): void
    {
        $this->render('cuenta', [
            'title'   => 'Mi cuenta',
            'usuario' => $usuario,
            'errors'  => $errores,
        ], $status);
    }
}
