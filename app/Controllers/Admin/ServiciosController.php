<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Validator;
use App\Models\Servicio;

/** CRUD de servicios (página /servicios del sitio). */
final class ServiciosController extends AdminController
{
    protected $section = 'servicios';

    /** GET /admin/servicios */
    public function index(): void
    {
        $this->render('servicios/index', [
            'title'     => 'Servicios',
            'servicios' => (new Servicio())->todos(),
        ]);
    }

    /** GET /admin/servicios/nuevo */
    public function crear(): void
    {
        $this->requireAdmin();
        $this->formulario(['titulo' => '', 'descripcion' => '', 'icono' => 'service-web', 'orden' => '0', 'activo' => 1]);
    }

    /** POST /admin/servicios/guardar */
    public function guardar(): void
    {
        $this->requireAdmin();
        $datos   = $this->datos();
        $errores = $this->validar($datos, 0);

        if ($errores !== []) {
            $this->formulario($datos, $errores, 0, 422);
            return;
        }

        if (!$this->attempt(function () use ($datos): void {
            (new Servicio())->crear($datos);
        })) {
            $this->back('/admin/servicios', 'error', 'Error al guardar: no se pudo registrar el servicio.');
        }

        $this->back('/admin/servicios', 'success', 'Registro guardado correctamente.');
    }

    /** GET /admin/servicios/editar?id=N */
    public function editar(): void
    {
        $this->requireAdmin();
        $id       = $this->intParam('id');
        $servicio = (new Servicio())->buscar($id);

        if ($servicio === null) {
            $this->back('/admin/servicios', 'error', 'El servicio no existe o ya fue eliminado.');
        }

        $this->formulario($servicio, [], $id);
    }

    /** POST /admin/servicios/actualizar */
    public function actualizar(): void
    {
        $this->requireAdmin();
        $id     = $this->intParam('id');
        $modelo = new Servicio();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/servicios', 'error', 'El servicio no existe o ya fue eliminado.');
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
            $this->back('/admin/servicios', 'error', 'Error al guardar: no se pudo actualizar el servicio.');
        }

        $this->back('/admin/servicios', 'success', 'Registro actualizado correctamente.');
    }

    /** POST /admin/servicios/eliminar (los mensajes que lo pedían quedan "sin servicio") */
    public function eliminar(): void
    {
        $this->requireAdmin();
        $id     = $this->intParam('id');
        $modelo = new Servicio();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/servicios', 'error', 'El servicio no existe o ya fue eliminado.');
        }

        if (!$this->attempt(function () use ($modelo, $id): void {
            $modelo->eliminar($id);
        })) {
            $this->back('/admin/servicios', 'error', 'Error al guardar: no se pudo eliminar el servicio.');
        }

        $this->back('/admin/servicios', 'success', 'Registro eliminado correctamente.');
    }

    /** @return array<string, mixed> */
    private function datos(): array
    {
        return [
            'titulo'      => $this->input('titulo'),
            'descripcion' => $this->input('descripcion'),
            'icono'       => $this->input('icono'),
            'orden'       => $this->input('orden', '0'),
            'activo'      => $this->checkbox('activo'),
        ];
    }

    /** @param array<string, mixed> $datos @return array<string, string> */
    private function validar(array $datos, int $id): array
    {
        $v = new Validator($datos, ['titulo' => 'Título', 'descripcion' => 'Descripción', 'icono' => 'Icono', 'orden' => 'Orden']);

        $v->required('titulo', 'descripcion', 'icono', 'orden')
          ->min('titulo', 3)->max('titulo', 80)
          ->min('descripcion', 10)->max('descripcion', 255)
          ->in('icono', array_keys(Servicio::ICONOS))
          ->integer('orden', 0, 999)
          ->check(
              (string) $datos['titulo'] !== '' && (new Servicio())->existeTitulo((string) $datos['titulo'], $id),
              'titulo',
              'Ya existe un servicio con ese título.'
          );

        return $v->errors();
    }

    /**
     * @param array<string, mixed>  $servicio
     * @param array<string, string> $errores
     */
    private function formulario(array $servicio, array $errores = [], int $id = 0, int $status = 200): void
    {
        $this->render('servicios/form', [
            'title'    => $id > 0 ? 'Editar servicio' : 'Nuevo servicio',
            'servicio' => $servicio,
            'id'       => $id,
            'iconos'   => Servicio::ICONOS,
            'errors'   => $errores,
        ], $status);
    }
}
