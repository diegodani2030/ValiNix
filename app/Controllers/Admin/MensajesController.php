<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Models\Mensaje;

/**
 * Mensajes que llegan desde el formulario de /contacto (solo Administrador):
 * consultar con buscador y filtro, ver el detalle, marcar leído/no leído y eliminar.
 */
final class MensajesController extends AdminController
{
    protected $section = 'mensajes';

    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    /** GET /admin/mensajes?buscar=...&estado=nuevos|leidos */
    public function index(): void
    {
        $buscar = is_string($_GET['buscar'] ?? null) ? mb_substr(trim($_GET['buscar']), 0, 100) : '';
        $estado = is_string($_GET['estado'] ?? null) && in_array($_GET['estado'], ['nuevos', 'leidos'], true) ? $_GET['estado'] : '';

        $this->render('mensajes/index', [
            'title'    => 'Mensajes',
            'mensajes' => (new Mensaje())->listar($buscar, $estado),
            'buscar'   => $buscar,
            'estado'   => $estado,
        ]);
    }

    /** GET /admin/mensajes/ver?id=N (al abrirlo queda como leído) */
    public function ver(): void
    {
        $id      = $this->intParam('id');
        $modelo  = new Mensaje();
        $mensaje = $modelo->buscar($id);

        if ($mensaje === null) {
            $this->back('/admin/mensajes', 'error', 'El mensaje no existe o ya fue eliminado.');
        }

        if ((int) $mensaje['leido'] === 0) {
            $modelo->marcarLeido($id);
            $mensaje['leido'] = 1;
        }

        $this->render('mensajes/ver', [
            'title'   => 'Mensaje de ' . $mensaje['nombre'],
            'mensaje' => $mensaje,
        ]);
    }

    /** POST /admin/mensajes/estado: alterna entre leído y no leído. */
    public function estado(): void
    {
        $id      = $this->intParam('id');
        $modelo  = new Mensaje();
        $mensaje = $modelo->buscar($id);

        if ($mensaje === null) {
            $this->back('/admin/mensajes', 'error', 'El mensaje no existe o ya fue eliminado.');
        }

        $leido = (int) $mensaje['leido'] === 0;
        if (!$this->attempt(function () use ($modelo, $id, $leido): void {
            $modelo->marcarLeido($id, $leido);
        })) {
            $this->back('/admin/mensajes', 'error', 'Error al guardar: no se pudo cambiar el estado.');
        }

        $this->back('/admin/mensajes', 'success', 'Registro actualizado correctamente.');
    }

    /** POST /admin/mensajes/eliminar */
    public function eliminar(): void
    {
        $id     = $this->intParam('id');
        $modelo = new Mensaje();

        if ($modelo->buscar($id) === null) {
            $this->back('/admin/mensajes', 'error', 'El mensaje no existe o ya fue eliminado.');
        }

        if (!$this->attempt(function () use ($modelo, $id): void {
            $modelo->eliminar($id);
        })) {
            $this->back('/admin/mensajes', 'error', 'Error al guardar: no se pudo eliminar el mensaje.');
        }

        $this->back('/admin/mensajes', 'success', 'Registro eliminado correctamente.');
    }
}
