<?php
/**
 * Formulario de usuario: datos de la cuenta y asignación de rol (permisos).
 *
 * @var array<string, mixed>              $usuario
 * @var int                               $id
 * @var array<int, array<string, mixed>>  $roles
 * @var array<string, string>             $errors
 */

$accion = $id > 0 ? 'admin/usuarios/actualizar' : 'admin/usuarios/guardar';
?>
            <form class="admin-card admin-form" action="<?= e(url($accion)) ?>" method="POST" novalidate data-validate autocomplete="off">
<?php if ($errors !== []): ?>
                <div class="admin-alert admin-alert--error" role="alert">Revisa los campos marcados: hay datos obligatorios o con formato incorrecto.</div>
<?php endif; ?>
                <?= csrf_field() ?>

                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="nombre">Nombre completo *</label>
                        <input type="text" id="nombre" name="nombre" required minlength="3" maxlength="100" value="<?= e($usuario['nombre'] ?? '') ?>"<?= invalid_attr($errors, 'nombre') ?>>
                        <?= field_error($errors, 'nombre') ?>

                    </div>
                    <div class="form-group">
                        <label for="nombre_usuario">Usuario *</label>
                        <input type="text" id="nombre_usuario" name="nombre_usuario" required minlength="3" maxlength="40" pattern="[A-Za-z0-9._\-]+" value="<?= e($usuario['nombre_usuario'] ?? '') ?>"<?= invalid_attr($errors, 'nombre_usuario') ?>>
                        <span class="field-hint">Sin espacios: letras, números, punto, guion o guion bajo.</span>
                        <?= field_error($errors, 'nombre_usuario') ?>

                    </div>
                </div>

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="correo">Correo electrónico *</label>
                        <input type="email" id="correo" name="correo" required maxlength="150" value="<?= e($usuario['correo'] ?? '') ?>"<?= invalid_attr($errors, 'correo') ?>>
                        <?= field_error($errors, 'correo') ?>

                    </div>
                    <div class="form-group">
                        <label for="rol_id">Rol (permisos) *</label>
                        <select id="rol_id" name="rol_id" required<?= invalid_attr($errors, 'rol_id') ?>>
<?php foreach ($roles as $rol): ?>
                            <option value="<?= (int) $rol['id'] ?>"<?= (string) ($usuario['rol_id'] ?? '') === (string) $rol['id'] ? ' selected' : '' ?>><?= e($rol['nombre']) ?></option>
<?php endforeach; ?>
                        </select>
                        <?= field_error($errors, 'rol_id') ?>

                    </div>
                </div>

                <fieldset class="admin-fieldset">
                    <legend><?= $id > 0 ? 'Cambiar contraseña (opcional)' : 'Contraseña' ?></legend>
<?php if ($id > 0): ?>
                    <p class="field-hint">Déjala en blanco para conservar la contraseña actual.</p>
<?php endif; ?>
                    <div class="admin-form__row">
                        <div class="form-group">
                            <label for="contrasena">Contraseña<?= $id > 0 ? '' : ' *' ?></label>
                            <input type="password" id="contrasena" name="contrasena"<?= $id > 0 ? '' : ' required' ?> minlength="8" maxlength="72" autocomplete="new-password"<?= invalid_attr($errors, 'contrasena') ?>>
                            <span class="field-hint">Mínimo 8 caracteres.</span>
                            <?= field_error($errors, 'contrasena') ?>

                        </div>
                        <div class="form-group">
                            <label for="confirmar">Confirmar contraseña<?= $id > 0 ? '' : ' *' ?></label>
                            <input type="password" id="confirmar" name="confirmar" maxlength="72" autocomplete="new-password" data-match="contrasena"<?= invalid_attr($errors, 'confirmar') ?>>
                            <?= field_error($errors, 'confirmar') ?>

                        </div>
                    </div>
                </fieldset>

                <label class="admin-check">
                    <input type="checkbox" name="activo" value="1"<?= !empty($usuario['activo']) ? ' checked' : '' ?>>
                    Cuenta activa (puede iniciar sesión)
                </label>

                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--accent"><?= $id > 0 ? 'Guardar cambios' : 'Crear usuario' ?></button>
                    <a href="<?= e(url('admin/usuarios')) ?>" class="btn btn--ghost">Cancelar</a>
                </div>
            </form>
