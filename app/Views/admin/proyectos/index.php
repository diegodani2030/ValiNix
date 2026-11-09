<?php
/**
 * Lista de proyectos (consulta multitabla: proyecto + categoría + usuario).
 *
 * @var array<int, array<string, mixed>> $proyectos
 * @var array<int, string>               $categorias
 * @var int                              $filtro
 * @var bool                             $isAdmin
 */
?>
            <div class="admin-toolbar">
                <form class="admin-filter" method="GET" action="<?= e(url('admin/proyectos')) ?>">
                    <label for="categoria">Categoría</label>
                    <select id="categoria" name="categoria" data-autosubmit>
                        <option value="">Todas</option>
<?php foreach ($categorias as $id => $nombre): ?>
                        <option value="<?= (int) $id ?>"<?= $filtro === (int) $id ? ' selected' : '' ?>><?= e($nombre) ?></option>
<?php endforeach; ?>
                    </select>
                    <noscript><button type="submit" class="btn btn--ghost btn--sm">Filtrar</button></noscript>
                </form>
<?php if ($isAdmin): ?>
                <a href="<?= e(url('admin/proyectos/nuevo')) ?>" class="btn btn--accent btn--sm">+ Nuevo proyecto</a>
<?php endif; ?>
            </div>

            <div class="admin-card">
<?php if ($proyectos === []): ?>
                <p class="admin-muted">No hay proyectos<?= $filtro > 0 ? ' en esta categoría' : '' ?>.</p>
<?php else: ?>
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th><th>Proyecto</th><th>Categoría</th><th>Enlace</th><th>Registrado por</th>
<?php if ($isAdmin): ?><th class="admin-table__actions">Acciones</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
<?php foreach ($proyectos as $p): ?>
                            <tr>
                                <td><?= (int) $p['id'] ?></td>
                                <td>
                                    <strong><?= e($p['titulo']) ?></strong>
                                    <span class="admin-table__sub"><?= e($p['descripcion']) ?></span>
                                </td>
                                <td><span class="admin-tag"><?= e($p['categoria']) ?></span></td>
                                <td><?= $p['enlace'] !== '#' ? '<a href="' . e($p['enlace']) . '" target="_blank" rel="noopener">Abrir ↗</a>' : '<span class="admin-muted">—</span>' ?></td>
                                <td><?= e($p['registrado_por'] ?? '—') ?></td>
<?php if ($isAdmin): ?>
                                <td class="admin-table__actions">
                                    <a href="<?= e(url('admin/proyectos/editar') . '?id=' . (int) $p['id']) ?>" class="btn btn--ghost btn--xs">Editar</a>
                                    <form action="<?= e(url('admin/proyectos/eliminar')) ?>" method="POST" data-confirm="¿Eliminar el proyecto «<?= e($p['titulo']) ?>»? Esta acción no se puede deshacer.">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
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
