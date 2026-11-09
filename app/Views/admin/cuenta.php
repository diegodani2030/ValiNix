<?php
/**
 * Mi cuenta: datos propios y cambio de contraseña.
 *
 * @var array<string, mixed>  $usuario
 * @var array<string, string> $errors
 */
?>
            <form class="admin-card admin-form" action="<?= e(url('admin/cuenta')) ?>" method="POST" novalidate data-validate>
<?php if ($errors !== []): ?>
                <div class="admin-alert admin-alert--error" role="alert">Revisa los campos marcados.</div>
<?php endif; ?>
                <?= csrf_field() ?>

                <dl class="admin-details">
                    <div><dt>Usuario</dt><dd><?= e($usuario['nombre_usuario'] ?? '') ?></dd></div>
                    <div><dt>Rol</dt><dd><?= e($usuario['rol'] ?? '') ?></dd></div>
                </dl>

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="nombre">Nombre completo *</label>
                        <input type="text" id="nombre" name="nombre" required minlength="3" maxlength="100" value="<?= e($usuario['nombre'] ?? '') ?>"<?= invalid_attr($errors, 'nombre') ?>>
                        <?= field_error($errors, 'nombre') ?>

                    </div>
                    <div class="form-group">
                        <label for="correo">Correo electrónico *</label>
                        <input type="email" id="correo" name="correo" required maxlength="150" value="<?= e($usuario['correo'] ?? '') ?>"<?= invalid_attr($errors, 'correo') ?>>
                        <?= field_error($errors, 'correo') ?>

                    </div>
                </div>

                <fieldset class="admin-fieldset">
                    <legend>Cambiar contraseña (opcional)</legend>
                    <div class="form-group">
                        <label for="actual">Contraseña actual</label>
                        <input type="password" id="actual" name="actual" maxlength="72" autocomplete="current-password"<?= invalid_attr($errors, 'actual') ?>>
                        <?= field_error($errors, 'actual') ?>

                    </div>
                    <div class="admin-form__row">
                        <div class="form-group">
                            <label for="contrasena">Nueva contraseña</label>
                            <input type="password" id="contrasena" name="contrasena" minlength="8" maxlength="72" autocomplete="new-password"<?= invalid_attr($errors, 'contrasena') ?>>
                            <span class="field-hint">Mínimo 8 caracteres.</span>
                            <?= field_error($errors, 'contrasena') ?>

                        </div>
                        <div class="form-group">
                            <label for="confirmar">Confirmar nueva contraseña</label>
                            <input type="password" id="confirmar" name="confirmar" maxlength="72" autocomplete="new-password" data-match="contrasena"<?= invalid_attr($errors, 'confirmar') ?>>
                            <?= field_error($errors, 'confirmar') ?>

                        </div>
                    </div>
                </fieldset>

                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--accent">Guardar cambios</button>
                </div>
            </form>
