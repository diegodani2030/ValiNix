<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Validator;
use App\Models\Testimonio;

/** CRUD de testimonios (sección de opiniones en el inicio). */
final class TestimoniosController extends AdminController
{
    protected $section = 'testimonios';

    /** GET /admin/testimonios */
    public function index(): void
    {
        $this->render('testimonios/index', [
            'title'       => 'Testimonios',
            'testimonios' => (new Testimonio())->todos(),
        ]);
    }

    /** GET /admin/testimonios/nuevo */
    public function crear(): void
    {
        $this->requireAdmin();
        $this->formulario(['autor' => '', 'empresa' => '', 'calificacion' => '5', 'comentario' => '', 'orden' => '0']);
    }

    /** POST /admin/testimonios/guardar */
    public function guardar(): void
    {
        $this->requireAdmin();
        $datos   = $this->datos();
        $errores = $this->validar($datos);

        if ($errores !== []) {
            $this->formulario($datos, $errores, 0, 422);
            return;
        }

        if (!$this->attempt(function () use ($datos): void {
            (new Testimonio())->crear($datos);
        })) {
            $this->back('/admin/testimonios', 'error', 'Error al guardar: no se pudo registrar el testimonio.');
        }

        $this->back('/admin/testimonios', 'success', 'Registro guardado correctamente.');
    }

    /** GET /admin/testimonios/editar?id=N */
    public function editar(): void
    {
        $this->requireAdmin();
        $id         = $this->intParam('id');
        $testimonio = (new Testimonio())->buscar($id);

        if ($testimonio === null) {
            $this->back('/admin/testimonios', 'error', 'El testimonio no existe o ya fue eliminado.');
        }

        $this->formulario($testimonio, [], $id);
    }

    /** POST /admin/testimonios/actualizar */
    public function actualizar(): void
    {
        $this->requireAdmin();
        $id     = $this->intParam('id');
        $modelo = new Testimonio();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/testimonios', 'error', 'El testimonio no existe o ya fue eliminado.');
        }

        $datos   = $this->datos();
        $errores = $this->validar($datos);
        if ($errores !== []) {
            $this->formulario($datos, $errores, $id, 422);
            return;
        }

        if (!$this->attempt(function () use ($modelo, $id, $datos): void {
            $modelo->actualizar($id, $datos);
        })) {
            $this->back('/admin/testimonios', 'error', 'Error al guardar: no se pudo actualizar el testimonio.');
        }

        $this->back('/admin/testimonios', 'success', 'Registro actualizado correctamente.');
    }

    /** POST /admin/testimonios/eliminar */
    public function eliminar(): void
    {
        $this->requireAdmin();
        $id     = $this->intParam('id');
        $modelo = new Testimonio();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/testimonios', 'error', 'El testimonio no existe o ya fue eliminado.');
        }

        if (!$this->attempt(function () use ($modelo, $id): void {
            $modelo->eliminar($id);
        })) {
            $this->back('/admin/testimonios', 'error', 'Error al guardar: no se pudo eliminar el testimonio.');
        }

        $this->back('/admin/testimonios', 'success', 'Registro eliminado correctamente.');
    }

    /** @return array<string, string> */
    private function datos(): array
    {
        return [
            'autor'        => $this->input('autor'),
            'empresa'      => $this->input('empresa'),
            'calificacion' => $this->input('calificacion', '5'),
            'comentario'   => $this->input('comentario'),
            'orden'        => $this->input('orden', '0'),
        ];
    }

    /** @param array<string, string> $datos @return array<string, string> */
    private function validar(array $datos): array
    {
        $v = new Validator($datos, [
            'autor'        => 'Nombre del cliente',
            'empresa'      => 'Empresa',
            'calificacion' => 'Calificación',
            'comentario'   => 'Comentario',
            'orden'        => 'Orden',
        ]);

        $v->required('autor', 'empresa', 'calificacion', 'comentario', 'orden')
          ->min('autor', 3)->max('autor', 100)
          ->max('empresa', 100)
          ->integer('calificacion', 1, 5)
          ->min('comentario', 10)->max('comentario', 500)
          ->integer('orden', 0, 999);

        return $v->errors();
    }

    /**
     * @param array<string, mixed>  $testimonio
     * @param array<string, string> $errores
     */
    private function formulario(array $testimonio, array $errores = [], int $id = 0, int $status = 200): void
    {
        $this->render('testimonios/form', [
            'title'      => $id > 0 ? 'Editar testimonio' : 'Nuevo testimonio',
            'testimonio' => $testimonio,
            'id'         => $id,
            'errors'     => $errores,
        ], $status);
    }
}
