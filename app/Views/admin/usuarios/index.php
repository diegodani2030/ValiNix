<?php
/**
 * Gestión de usuarios (consulta multitabla: usuarios + roles).
 *
 * @var array<int, array<string, mixed>> $usuarios
 * @var int|null                         $yo
 */
?>
            <div class="admin-toolbar">
                <p class="admin-muted">El <strong>Administrador</strong> tiene acceso total. El <strong>Usuario registrado</strong> solo consulta información y administra su cuenta.</p>
                <a href="<?= e(url('admin/usuarios/nuevo')) ?>" class="btn btn--accent btn--sm">+ Nuevo usuario</a>
            </div>

            <div class="admin-card">
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Nombre</th><th>Usuario</th><th>Correo</th><th>Rol</th><th>Estado</th><th class="admin-table__actions">Acciones</th></tr>
                        </thead>
                        <tbody>
<?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><strong><?= e($u['nombre']) ?></strong><?= (int) $u['id'] === $yo ? ' <span class="admin-tag">Tú</span>' : '' ?></td>
                                <td><?= e($u['nombre_usuario']) ?></td>
                                <td><?= e($u['correo']) ?></td>
                                <td><span class="admin-role admin-role--<?= (int) $u['rol_id'] === \App\Models\Rol::ADMINISTRADOR ? 'admin' : 'user' ?>"><?= e($u['rol']) ?></span></td>
                                <td><?= (int) $u['activo'] === 1 ? '<span class="admin-tag admin-tag--ok">Activo</span>' : '<span class="admin-tag">Inactivo</span>' ?></td>
                                <td class="admin-table__actions">
                                    <a href="<?= e(url('admin/usuarios/editar') . '?id=' . (int) $u['id']) ?>" class="btn btn--ghost btn--xs">Editar</a>
<?php if ((int) $u['id'] !== $yo): ?>
                                    <form action="<?= e(url('admin/usuarios/eliminar')) ?>" method="POST" data-confirm="¿Eliminar al usuario «<?= e($u['nombre_usuario']) ?>»? Ya no podrá iniciar sesión.">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <button type="submit" class="btn btn--danger btn--xs">Eliminar</button>
                                    </form>
<?php endif; ?>
                                </td>
                            </tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
