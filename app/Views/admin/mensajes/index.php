<?php
/**
 * Mensajes del formulario de contacto (consulta multitabla con filtros).
 *
 * @var array<int, array<string, mixed>> $mensajes
 * @var string                           $buscar
 * @var string                           $estado
 */
?>
            <form class="admin-toolbar admin-search" method="GET" action="<?= e(url('admin/mensajes')) ?>" role="search">
                <div class="form-group">
                    <label for="buscar">Buscar</label>
                    <input type="search" id="buscar" name="buscar" maxlength="100" placeholder="Nombre, correo o asunto" value="<?= e($buscar) ?>">
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="">Todos</option>
                        <option value="nuevos"<?= $estado === 'nuevos' ? ' selected' : '' ?>>Sin leer</option>
                        <option value="leidos"<?= $estado === 'leidos' ? ' selected' : '' ?>>Leídos</option>
                    </select>
                </div>
                <button type="submit" class="btn btn--ghost btn--sm">Filtrar</button>
<?php if ($buscar !== '' || $estado !== ''): ?>
                <a href="<?= e(url('admin/mensajes')) ?>" class="admin-link">Quitar filtros</a>
<?php endif; ?>
            </form>

            <div class="admin-card">
<?php if ($mensajes === []): ?>
                <p class="admin-muted"><?= $buscar !== '' || $estado !== '' ? 'No hay mensajes que coincidan con la búsqueda.' : 'Todavía no han llegado mensajes desde el formulario de contacto.' ?></p>
<?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Fecha</th><th>De</th><th>Asunto</th><th>Servicio / plan</th><th>Estado</th><th class="admin-table__actions">Acciones</th></tr>
                        </thead>
                        <tbody>
<?php foreach ($mensajes as $m): ?>
                            <tr class="<?= (int) $m['leido'] === 0 ? 'is-unread' : '' ?>">
                                <td><?= e(date('d/m/Y H:i', (int) strtotime((string) $m['creado_en']))) ?></td>
                                <td><strong><?= e($m['nombre']) ?></strong><span class="admin-table__sub"><?= e($m['correo']) ?></span></td>
                                <td><a href="<?= e(url('admin/mensajes/ver') . '?id=' . (int) $m['id']) ?>"><?= e($m['asunto']) ?></a></td>
                                <td><?= e($m['servicio'] ?? '—') ?><span class="admin-table__sub"><?= e($m['plan'] ?? 'Sin plan') ?></span></td>
                                <td><?= (int) $m['leido'] === 1 ? '<span class="admin-tag">Leído</span>' : '<span class="admin-tag admin-tag--new">Nuevo</span>' ?></td>
                                <td class="admin-table__actions">
                                    <a href="<?= e(url('admin/mensajes/ver') . '?id=' . (int) $m['id']) ?>" class="btn btn--ghost btn--xs">Ver</a>
                                    <form action="<?= e(url('admin/mensajes/eliminar')) ?>" method="POST" data-confirm="¿Eliminar el mensaje de <?= e($m['nombre']) ?>?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                                        <button type="submit" class="btn btn--danger btn--xs">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
<?php endif; ?>
            </div>
