<?php
/**
 * @var array<int, array<string, mixed>> $testimonios
 * @var bool                             $isAdmin
 */
?>
            <div class="admin-toolbar">
                <p class="admin-muted">Se muestran en la página de inicio, en el orden indicado.</p>
<?php if ($isAdmin): ?>
                <a href="<?= e(url('admin/testimonios/nuevo')) ?>" class="btn btn--accent btn--sm">+ Nuevo testimonio</a>
<?php endif; ?>
            </div>

            <div class="admin-card">
<?php if ($testimonios === []): ?>
                <p class="admin-muted">No hay testimonios. La sección de opiniones no se muestra en el inicio hasta que agregues uno.</p>
<?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Orden</th><th>Cliente</th><th>Calificación</th><th>Comentario</th>
<?php if ($isAdmin): ?><th class="admin-table__actions">Acciones</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
<?php foreach ($testimonios as $t): ?>
<?php $estrellas = max(1, min(5, (int) $t['calificacion'])); ?>
                            <tr>
                                <td><?= (int) $t['orden'] ?></td>
                                <td><strong><?= e($t['autor']) ?></strong><span class="admin-table__sub"><?= e($t['empresa']) ?></span></td>
                                <td><span class="admin-stars" aria-label="<?= $estrellas ?> de 5"><?= str_repeat('★', $estrellas) . str_repeat('☆', 5 - $estrellas) ?></span></td>
                                <td class="admin-table__wide"><?= e($t['comentario']) ?></td>
<?php if ($isAdmin): ?>
                                <td class="admin-table__actions">
                                    <a href="<?= e(url('admin/testimonios/editar') . '?id=' . (int) $t['id']) ?>" class="btn btn--ghost btn--xs">Editar</a>
                                    <form action="<?= e(url('admin/testimonios/eliminar')) ?>" method="POST" data-confirm="¿Eliminar el testimonio de <?= e($t['autor']) ?>?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $t['id'] ?>">
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
