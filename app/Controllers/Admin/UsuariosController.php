<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Validator;
use App\Models\Rol;
use App\Models\Usuario;

/**
 * Gestión de usuarios (solo Administrador): crear, consultar, editar,
 * eliminar y asignar el rol (permisos) de cada cuenta.
 */
final class UsuariosController extends AdminController
{
    protected $section = 'usuarios';

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    /** GET /admin/usuarios */
    public function index(): void
    {
        $this->render('usuarios/index', [
            'title'    => 'Usuarios',
            'usuarios' => (new Usuario())->todos(),
            'yo'       => Auth::id(),
        ]);
    }

    /** GET /admin/usuarios/nuevo */
    public function crear(): void
    {
        $this->formulario([
            'nombre' => '', 'nombre_usuario' => '', 'correo' => '',
            'rol_id' => (string) Rol::USUARIO_REGISTRADO, 'activo' => 1,
        ]);
    }

    /** POST /admin/usuarios/guardar */
    public function guardar(): void
    {
        $datos   = $this->datos();
        $errores = $this->validar($datos, 0);

        if ($errores !== []) {
            $this->formulario($datos, $errores, 0, 422);
            return;
        }

        if (!$this->attempt(function () use ($datos): void {
            (new Usuario())->crear($datos);
        })) {
            $this->back('/admin/usuarios', 'error', 'Error al guardar: no se pudo registrar el usuario.');
        }

        $this->back('/admin/usuarios', 'success', 'Registro guardado correctamente.');
    }

    /** GET /admin/usuarios/editar?id=N */
    public function editar(): void
    {
        $id      = $this->intParam('id');
        $usuario = (new Usuario())->buscar($id);

        if ($usuario === null) {
            $this->back('/admin/usuarios', 'error', 'El usuario no existe o ya fue eliminado.');
        }

        $this->formulario($usuario, [], $id);
    }

    /** POST /admin/usuarios/actualizar */
    public function actualizar(): void
    {
        $id     = $this->intParam('id');
        $modelo = new Usuario();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/usuarios', 'error', 'El usuario no existe o ya fue eliminado.');
        }

        $datos   = $this->datos();
        $errores = $this->validar($datos, $id);
        if ($errores !== []) {
            $this->formulario($datos, $errores, $id, 422);
            return;
        }

        if (!$this->attempt(function () use ($modelo, $id, $datos): void {
            $modelo->actualizar($id, $datos);
        })) {
            $this->back('/admin/usuarios', 'error', 'Error al guardar: no se pudo actualizar el usuario.');
        }

        $this->back('/admin/usuarios', 'success', 'Registro actualizado correctamente.');
    }

    /** POST /admin/usuarios/eliminar */
    public function eliminar(): void
    {
        $id     = $this->intParam('id');
        $modelo = new Usuario();
        $usuario = $modelo->buscar($id);

        if ($usuario === null) {
            $this->back('/admin/usuarios', 'error', 'El usuario no existe o ya fue eliminado.');
        }
        if ($id === Auth::id()) {
            $this->back('/admin/usuarios', 'error', 'No puedes eliminar tu propia cuenta mientras la estás usando.');
        }
        if ((int) $usuario['rol_id'] === Rol::ADMINISTRADOR && $modelo->contarAdministradoresActivos($id) === 0) {
            $this->back('/admin/usuarios', 'error', 'No se puede eliminar el único administrador activo.');
        }

        if (!$this->attempt(function () use ($modelo, $id): void {
            $modelo->eliminar($id);
        })) {
            $this->back('/admin/usuarios', 'error', 'Error al guardar: no se pudo eliminar el usuario.');
        }

        $this->back('/admin/usuarios', 'success', 'Registro eliminado correctamente.');
    }

    /** @return array<string, mixed> */
    private function datos(): array
    {
        return [
            'nombre'         => $this->input('nombre'),
            'nombre_usuario' => $this->input('nombre_usuario'),
            'correo'         => mb_strtolower($this->input('correo')),
            'rol_id'         => $this->input('rol_id'),
            'activo'         => $this->checkbox('activo'),
            'contrasena'     => is_string($_POST['contrasena'] ?? null) ? $_POST['contrasena'] : '',
            'confirmar'      => is_string($_POST['confirmar'] ?? null) ? $_POST['confirmar'] : '',
        ];
    }

    /** @param array<string, mixed> $datos @return array<string, string> */
    private function validar(array $datos, int $id): array
    {
        $modelo = new Usuario();
        $esNuevo = $id === 0;

        $v = new Validator($datos, [
            'nombre'         => 'Nombre completo',
            'nombre_usuario' => 'Usuario',
            'correo'         => 'Correo electrónico',
            'rol_id'         => 'Rol',
            'contrasena'     => 'Contraseña',
        ]);

        $v->required('nombre', 'nombre_usuario', 'correo', 'rol_id')
          ->min('nombre', 3)->max('nombre', 100)
          ->min('nombre_usuario', 3)->max('nombre_usuario', 40)->username('nombre_usuario')
          ->max('correo', 150)->email('correo')
          ->in('rol_id', array_keys((new Rol())->opciones()))
          ->check($datos['nombre_usuario'] !== '' && $modelo->existe('nombre_usuario', (string) $datos['nombre_usuario'], $id), 'nombre_usuario', 'Ese nombre de usuario ya está registrado.')
          ->check($datos['correo'] !== '' && $modelo->existe('correo', (string) $datos['correo'], $id), 'correo', 'Ese correo ya está registrado.');

        // La contraseña es obligatoria al crear; al editar, vacía = no se cambia.
        if ($esNuevo) {
            $v->required('contrasena');
        }
        if ($datos['contrasena'] !== '') {
            $v->min('contrasena', 8)->max('contrasena', 72)->same('confirmar', 'contrasena');
        }

        // No dejar el sistema sin administradores.
        if (!$esNuevo) {
            $quitaAdmin = (int) $datos['rol_id'] !== Rol::ADMINISTRADOR || (int) $datos['activo'] !== 1;
            $eraAdmin   = (int) (($modelo->buscar($id) ?? [])['rol_id'] ?? 0) === Rol::ADMINISTRADOR;
            $v->check($id === Auth::id() && $quitaAdmin, 'rol_id', 'No puedes quitarte el rol de administrador ni desactivar tu propia cuenta.')
              ->check($eraAdmin && $quitaAdmin && $modelo->contarAdministradoresActivos($id) === 0, 'rol_id', 'Debe quedar al menos un administrador activo.');
        }

        return $v->errors();
    }

    /**
     * @param array<string, mixed>  $usuario
     * @param array<string, string> $errores
     */
    private function formulario(array $usuario, array $errores = [], int $id = 0, int $status = 200): void
    {
        $this->render('usuarios/form', [
            'title'   => $id > 0 ? 'Editar usuario' : 'Nuevo usuario',
            'usuario' => $usuario,
            'id'      => $id,
            'roles'   => (new Rol())->todos(),
            'errors'  => $errores,
        ], $status);
    }
}
