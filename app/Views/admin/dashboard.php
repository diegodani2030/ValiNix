<?php
/**
 * Inicio del panel.
 *
 * @var array<string, int|null>           $totales
 * @var array<int, array<string, mixed>>  $porCategoria
 * @var array<int, array<string, mixed>>  $porServicio
 * @var array<int, array<string, mixed>>  $recientes
 * @var bool                              $isAdmin
 * @var int                               $unread
 */

$tarjetas = [
    ['label' => 'Servicios activos', 'value' => $totales['servicios'], 'href' => 'admin/servicios'],
    ['label' => 'Proyectos',         'value' => $totales['proyectos'], 'href' => 'admin/proyectos'],
];
if ($isAdmin) {
    $tarjetas[] = ['label' => 'Mensajes', 'value' => $totales['mensajes'], 'href' => 'admin/mensajes', 'extra' => $unread . ' sin leer'];
    $tarjetas[] = ['label' => 'Usuarios', 'value' => $totales['usuarios'], 'href' => 'admin/usuarios'];
}

$maxCategoria = max(array_merge([1], array_map(static function (array $c): int { return (int) $c['total']; }, $porCategoria)));
$maxServicio  = max(array_merge([1], array_map(static function (array $s): int { return (int) $s['total']; }, $porServicio)));
?>
            <div class="admin-stats">
<?php foreach ($tarjetas as $tarjeta): ?>
                <a href="<?= e(url($tarjeta['href'])) ?>" class="admin-stat">
                    <span class="admin-stat__value"><?= (int) $tarjeta['value'] ?></span>
                    <span class="admin-stat__label"><?= e($tarjeta['label']) ?></span>
<?php if (!empty($tarjeta['extra'])): ?>
                    <span class="admin-stat__extra"><?= e($tarjeta['extra']) ?></span>
<?php endif; ?>
                </a>
<?php endforeach; ?>
            </div>

            <div class="admin-grid">
                <section class="admin-card">
                    <h2 class="admin-card__title">Proyectos por categoría</h2>
<?php if ($porCategoria === []): ?>
                    <p class="admin-muted">Aún no hay proyectos registrados.</p>
<?php else: ?>
                    <ul class="admin-bars">
<?php foreach ($porCategoria as $fila): ?>
                        <li>
                            <span class="admin-bars__label"><?= e($fila['nombre']) ?></span>
                            <span class="admin-bars__track"><span class="admin-bars__fill" style="width: <?= round((int) $fila['total'] / $maxCategoria * 100) ?>%"></span></span>
                            <span class="admin-bars__value"><?= (int) $fila['total'] ?></span>
                        </li>
<?php endforeach; ?>
                    </ul>
<?php endif; ?>
                </section>

<?php if ($isAdmin): ?>
                <section class="admin-card">
                    <h2 class="admin-card__title">Mensajes por servicio</h2>
<?php if ($porServicio === []): ?>
                    <p class="admin-muted">Todavía no han llegado mensajes desde el formulario de contacto.</p>
<?php else: ?>
                    <ul class="admin-bars">
<?php foreach ($porServicio as $fila): ?>
                        <li>
                            <span class="admin-bars__label"><?= e($fila['servicio']) ?></span>
                            <span class="admin-bars__track"><span class="admin-bars__fill admin-bars__fill--alt" style="width: <?= round((int) $fila['total'] / $maxServicio * 100) ?>%"></span></span>
                            <span class="admin-bars__value"><?= (int) $fila['total'] ?></span>
                        </li>
<?php endforeach; ?>
                    </ul>
<?php endif; ?>
                </section>
<?php else: ?>
                <section class="admin-card">
                    <h2 class="admin-card__title">Tu cuenta</h2>
                    <p class="admin-muted">Como <strong>Usuario registrado</strong> puedes consultar los proyectos, servicios y testimonios del sistema y administrar tu propia cuenta. Para modificar información, pide permisos a un administrador.</p>
                    <a href="<?= e(url('admin/cuenta')) ?>" class="btn btn--ghost btn--sm">Ir a Mi cuenta</a>
                </section>
<?php endif; ?>
            </div>

<?php if ($isAdmin): ?>
            <section class="admin-card">
                <div class="admin-card__head">
                    <h2 class="admin-card__title">Últimos mensajes</h2>
                    <a href="<?= e(url('admin/mensajes')) ?>" class="admin-link">Ver todos →</a>
                </div>
<?php if ($recientes === []): ?>
                <p class="admin-muted">No hay mensajes todavía.</p>
<?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead><tr><th>Fecha</th><th>Nombre</th><th>Asunto</th><th>Estado</th></tr></thead>
                        <tbody>
<?php foreach ($recientes as $m): ?>
                            <tr>
                                <td><?= e(date('d/m/Y H:i', (int) strtotime((string) $m['creado_en']))) ?></td>
                                <td><a href="<?= e(url('admin/mensajes/ver') . '?id=' . (int) $m['id']) ?>"><?= e($m['nombre']) ?></a></td>
                                <td><?= e($m['asunto']) ?></td>
                                <td><?= (int) $m['leido'] === 1 ? '<span class="admin-tag">Leído</span>' : '<span class="admin-tag admin-tag--new">Nuevo</span>' ?></td>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
<?php endif; ?>
            </section>
<?php endif; ?>
