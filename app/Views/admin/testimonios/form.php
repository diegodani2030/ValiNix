<?php
/**
 * Formulario de testimonio. Campos = columnas de la tabla testimonios.
 *
 * @var array<string, mixed>  $testimonio
 * @var int                   $id
 * @var array<string, string> $errors
 */

$accion = $id > 0 ? 'admin/testimonios/actualizar' : 'admin/testimonios/guardar';
?>
            <form class="admin-card admin-form" action="<?= e(url($accion)) ?>" method="POST" novalidate data-validate>
<?php if ($errors !== []): ?>
                <div class="admin-alert admin-alert--error" role="alert">Revisa los campos marcados: hay datos obligatorios o con formato incorrecto.</div>
<?php endif; ?>
                <?= csrf_field() ?>

                <input type="hidden" name="id" value="<?= (int) $id ?>">

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="autor">Nombre del cliente *</label>
                        <input type="text" id="autor" name="autor" required minlength="3" maxlength="100" value="<?= e($testimonio['autor'] ?? '') ?>"<?= invalid_attr($errors, 'autor') ?>>
                        <?= field_error($errors, 'autor') ?>

                    </div>
                    <div class="form-group">
                        <label for="empresa">Empresa *</label>
                        <input type="text" id="empresa" name="empresa" required maxlength="100" value="<?= e($testimonio['empresa'] ?? '') ?>"<?= invalid_attr($errors, 'empresa') ?>>
                        <?= field_error($errors, 'empresa') ?>

                    </div>
                </div>

                <div class="form-group">
                    <label for="comentario">Comentario *</label>
                    <textarea id="comentario" name="comentario" rows="4" required minlength="10" maxlength="500"<?= invalid_attr($errors, 'comentario') ?>><?= e($testimonio['comentario'] ?? '') ?></textarea>
                    <?= field_error($errors, 'comentario') ?>

                </div>

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="calificacion">Calificación (1 a 5) *</label>
                        <input type="number" id="calificacion" name="calificacion" required min="1" max="5" step="1" value="<?= e($testimonio['calificacion'] ?? '5') ?>"<?= invalid_attr($errors, 'calificacion') ?>>
                        <?= field_error($errors, 'calificacion') ?>

                    </div>
                    <div class="form-group">
                        <label for="orden">Orden *</label>
                        <input type="number" id="orden" name="orden" required min="0" max="999" step="1" value="<?= e($testimonio['orden'] ?? '0') ?>"<?= invalid_attr($errors, 'orden') ?>>
                        <?= field_error($errors, 'orden') ?>

                    </div>
                </div>

                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--accent"><?= $id > 0 ? 'Guardar cambios' : 'Guardar testimonio' ?></button>
                    <a href="<?= e(url('admin/testimonios')) ?>" class="btn btn--ghost">Cancelar</a>
                </div>
            </form>
