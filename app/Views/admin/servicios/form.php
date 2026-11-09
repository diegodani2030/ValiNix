<?php
/**
 * Formulario de servicio. Campos = columnas de la tabla servicios.
 *
 * @var array<string, mixed>  $servicio
 * @var int                   $id
 * @var array<string, string> $iconos
 * @var array<string, string> $errors
 */

$accion = $id > 0 ? 'admin/servicios/actualizar' : 'admin/servicios/guardar';
?>
            <form class="admin-card admin-form" action="<?= e(url($accion)) ?>" method="POST" novalidate data-validate>
<?php if ($errors !== []): ?>
                <div class="admin-alert admin-alert--error" role="alert">Revisa los campos marcados: hay datos obligatorios o con formato incorrecto.</div>
<?php endif; ?>
                <?= csrf_field() ?>

                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" required minlength="3" maxlength="80" value="<?= e($servicio['titulo'] ?? '') ?>"<?= invalid_attr($errors, 'titulo') ?>>
                        <?= field_error($errors, 'titulo') ?>

                    </div>
                    <div class="form-group">
                        <label for="icono">Icono *</label>
                        <select id="icono" name="icono" required<?= invalid_attr($errors, 'icono') ?>>
<?php foreach ($iconos as $value => $label): ?>
                            <option value="<?= e($value) ?>"<?= ($servicio['icono'] ?? '') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                        </select>
                        <?= field_error($errors, 'icono') ?>

                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required minlength="10" maxlength="255"<?= invalid_attr($errors, 'descripcion') ?>><?= e($servicio['descripcion'] ?? '') ?></textarea>
                    <?= field_error($errors, 'descripcion') ?>

                </div>

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="orden">Orden *</label>
                        <input type="number" id="orden" name="orden" required min="0" max="999" step="1" value="<?= e($servicio['orden'] ?? '0') ?>"<?= invalid_attr($errors, 'orden') ?>>
                        <span class="field-hint">Número menor = aparece primero.</span>
                        <?= field_error($errors, 'orden') ?>

                    </div>
                    <div class="form-group form-group--check">
                        <label class="admin-check">
                            <input type="checkbox" name="activo" value="1"<?= !empty($servicio['activo']) ? ' checked' : '' ?>>
                            Mostrar en el sitio web
                        </label>
                    </div>
                </div>

                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--accent"><?= $id > 0 ? 'Guardar cambios' : 'Guardar servicio' ?></button>
                    <a href="<?= e(url('admin/servicios')) ?>" class="btn btn--ghost">Cancelar</a>
                </div>
            </form>
