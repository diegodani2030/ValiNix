<?php
/**
 * @var array<int, array<string, mixed>> $servicios
 * @var bool                             $isAdmin
 */
?>
            <div class="admin-toolbar">
                <p class="admin-muted">Los servicios activos se muestran en la página <a href="<?= e(url('servicios')) ?>" target="_blank" rel="noopener">/servicios</a> y en el formulario de contacto.</p>
<?php if ($isAdmin): ?>
                <a href="<?= e(url('admin/servicios/nuevo')) ?>" class="btn btn--accent btn--sm">+ Nuevo servicio</a>
<?php endif; ?>
            </div>

            <div class="admin-card">
<?php if ($servicios === []): ?>
                <p class="admin-muted">No hay servicios registrados.</p>
<?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Orden</th><th>Servicio</th><th>Estado</th><th>Mensajes</th>
<?php if ($isAdmin): ?><th class="admin-table__actions">Acciones</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
<?php foreach ($servicios as $s): ?>
                            <tr>
                                <td><?= (int) $s['orden'] ?></td>
                                <td>
                                    <span class="admin-icon"><?= icon((string) $s['icono']) ?></span>
                                    <strong><?= e($s['titulo']) ?></strong>
                                    <span class="admin-table__sub"><?= e($s['descripcion']) ?></span>
                                </td>
                                <td><?= (int) $s['activo'] === 1 ? '<span class="admin-tag admin-tag--ok">Visible</span>' : '<span class="admin-tag">Oculto</span>' ?></td>
                                <td><?= (int) $s['mensajes'] ?></td>
<?php if ($isAdmin): ?>
                                <td class="admin-table__actions">
                                    <a href="<?= e(url('admin/servicios/editar') . '?id=' . (int) $s['id']) ?>" class="btn btn--ghost btn--xs">Editar</a>
                                    <form action="<?= e(url('admin/servicios/eliminar')) ?>" method="POST" data-confirm="¿Eliminar el servicio «<?= e($s['titulo']) ?>»? Los mensajes que lo pedían quedarán sin servicio.">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                                        <button type="submit" class="btn btn--danger btn--xs">Eliminar</button>
                                    </form>
                                </td>
<?php endif; ?>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
<?php endif; ?>
            </div>
