<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Validator;
use App\Models\Categoria;
use App\Models\Proyecto;

/**
 * CRUD completo de proyectos (portafolio):
 *   Create → crear() + guardar()
 *   Read   → index()
 *   Update → editar() + actualizar()
 *   Delete → eliminar()
 */
final class ProyectosController extends AdminController
{
    protected $section = 'proyectos';

    /** GET /admin/proyectos (cualquier usuario con sesión puede consultar) */
    public function index(): void
    {
        $categorias = new Categoria();
        $filtro     = $this->intParam('categoria');
        $proyectos  = new Proyecto();

        $this->render('proyectos/index', [
            'title'      => 'Proyectos',
            'proyectos'  => $filtro > 0 ? $proyectos->porCategoria($filtro) : $proyectos->todos(),
            'categorias' => $categorias->opciones(),
            'filtro'     => $filtro,
        ]);
    }

    /** GET /admin/proyectos/nuevo */
    public function crear(): void
    {
        $this->requireAdmin();
        $this->formulario(['categoria_id' => '', 'titulo' => '', 'descripcion' => '', 'enlace' => '#', 'color' => '1']);
    }

    /** POST /admin/proyectos/guardar */
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
            (new Proyecto())->crear($datos, Auth::id());
        })) {
            $this->back('/admin/proyectos', 'error', 'Error al guardar: no se pudo registrar el proyecto.');
        }

        $this->back('/admin/proyectos', 'success', 'Registro guardado correctamente.');
    }

    /** GET /admin/proyectos/editar?id=N */
    public function editar(): void
    {
        $this->requireAdmin();
        $id       = $this->intParam('id');
        $proyecto = (new Proyecto())->buscar($id);

        if ($proyecto === null) {
            $this->back('/admin/proyectos', 'error', 'El proyecto no existe o ya fue eliminado.');
        }

        $this->formulario($proyecto, [], $id);
    }

    /** POST /admin/proyectos/actualizar */
    public function actualizar(): void
    {
        $this->requireAdmin();
        $id     = $this->intParam('id');
        $modelo = new Proyecto();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/proyectos', 'error', 'El proyecto no existe o ya fue eliminado.');
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
            $this->back('/admin/proyectos', 'error', 'Error al guardar: no se pudo actualizar el proyecto.');
        }

        $this->back('/admin/proyectos', 'success', 'Registro actualizado correctamente.');
    }

    /** POST /admin/proyectos/eliminar */
    public function eliminar(): void
    {
        $this->requireAdmin();
        $id     = $this->intParam('id');
        $modelo = new Proyecto();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/proyectos', 'error', 'El proyecto no existe o ya fue eliminado.');
        }

        if (!$this->attempt(function () use ($modelo, $id): void {
            $modelo->eliminar($id);
        })) {
            $this->back('/admin/proyectos', 'error', 'Error al guardar: no se pudo eliminar el proyecto.');
        }

        $this->back('/admin/proyectos', 'success', 'Registro eliminado correctamente.');
    }

    /** @return array<string, string> */
    private function datos(): array
    {
        return [
            'categoria_id' => $this->input('categoria_id'),
            'titulo'       => $this->input('titulo'),
            'descripcion'  => $this->input('descripcion'),
            'enlace'       => $this->input('enlace', '#'),
            'color'        => $this->input('color', '1'),
        ];
    }

    /** @param array<string, string> $datos @return array<string, string> */
    private function validar(array $datos): array
    {
        $v = new Validator($datos, [
            'categoria_id' => 'Categoría',
            'titulo'       => 'Título',
            'descripcion'  => 'Descripción',
            'enlace'       => 'Enlace',
            'color'        => 'Color de la miniatura',
        ]);

        $v->required('titulo', 'categoria_id', 'descripcion', 'color')
          ->min('titulo', 3)->max('titulo', 100)
          ->min('descripcion', 10)->max('descripcion', 255)
          ->in('categoria_id', array_keys((new Categoria())->opciones()))
          ->integer('color', 1, 6)
          ->max('enlace', 255)->url('enlace');

        return $v->errors();
    }

    /**
     * @param array<string, mixed>  $proyecto
     * @param array<string, string> $errores
     */
    private function formulario(array $proyecto, array $errores = [], int $id = 0, int $status = 200): void
    {
        $this->render('proyectos/form', [
            'title'      => $id > 0 ? 'Editar proyecto' : 'Nuevo proyecto',
            'proyecto'   => $proyecto,
            'id'         => $id,
            'categorias' => (new Categoria())->opciones(),
            'errors'     => $errores,
        ], $status);
    }
}
