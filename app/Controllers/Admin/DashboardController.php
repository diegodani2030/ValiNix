<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Models\Categoria;
use App\Models\Mensaje;
use App\Models\Proyecto;
use App\Models\Servicio;
use App\Models\Usuario;

/** GET /admin: resumen del sistema. */
final class DashboardController extends AdminController
{
    public function index(): void
    {
        $isAdmin = Auth::isAdmin();
        $mensajes = new Mensaje();

        $this->render('dashboard', [
            'title'      => 'Inicio del panel',
            'totales'    => [
                'servicios' => (new Servicio())->contar(),
                'proyectos' => (new Proyecto())->contar(),
                'mensajes'  => $isAdmin ? $mensajes->contar() : null,
                'usuarios'  => $isAdmin ? (new Usuario())->contar() : null,
            ],
            'porCategoria' => (new Categoria())->conProyectos(),
            'porServicio'  => $isAdmin ? $mensajes->porServicio() : [],
            'recientes'    => $isAdmin ? $mensajes->listar('', '', 5) : [],
        ]);
    }
}
