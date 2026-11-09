<?php
/**
 * Formulario de proyecto (nuevo y editar). Campos = columnas de la tabla proyectos.
 *
 * @var array<string, mixed>  $proyecto
 * @var int                   $id         0 = nuevo
 * @var array<int, string>    $categorias
 * @var array<string, string> $errors
 */

$accion = $id > 0 ? 'admin/proyectos/actualizar' : 'admin/proyectos/guardar';
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
                        <input type="text" id="titulo" name="titulo" required minlength="3" maxlength="100" value="<?= e($proyecto['titulo'] ?? '') ?>"<?= invalid_attr($errors, 'titulo') ?>>
                        <?= field_error($errors, 'titulo') ?>

                    </div>
                    <div class="form-group">
                        <label for="categoria_id">Categoría *</label>
                        <select id="categoria_id" name="categoria_id" required<?= invalid_attr($errors, 'categoria_id') ?>>
                            <option value="">Selecciona una categoría</option>
<?php foreach ($categorias as $value => $label): ?>
                            <option value="<?= (int) $value ?>"<?= (string) ($proyecto['categoria_id'] ?? '') === (string) $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                        </select>
                        <?= field_error($errors, 'categoria_id') ?>

                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required minlength="10" maxlength="255"<?= invalid_attr($errors, 'descripcion') ?>><?= e($proyecto['descripcion'] ?? '') ?></textarea>
                    <span class="field-hint">Entre 10 y 255 caracteres.</span>
                    <?= field_error($errors, 'descripcion') ?>

                </div>

                <div class="admin-form__row">
                    <div class="form-group">
                        <label for="enlace">Enlace del proyecto</label>
                        <input type="text" inputmode="url" id="enlace" name="enlace" maxlength="255" placeholder="https://..." value="<?= e($proyecto['enlace'] ?? '#') ?>"<?= invalid_attr($errors, 'enlace') ?>>
                        <span class="field-hint">Escribe # si el proyecto no tiene sitio publicado.</span>
                        <?= field_error($errors, 'enlace') ?>

                    </div>
                    <div class="form-group">
                        <label for="color">Color de la miniatura *</label>
                        <select id="color" name="color" required<?= invalid_attr($errors, 'color') ?>>
<?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?= $i ?>"<?= (string) ($proyecto['color'] ?? '1') === (string) $i ? ' selected' : '' ?>>Estilo <?= $i ?></option>
<?php endfor; ?>
                        </select>
                        <?= field_error($errors, 'color') ?>

                    </div>
                </div>

                <div class="admin-form__actions">
                    <button type="submit" class="btn btn--accent"><?= $id > 0 ? 'Guardar cambios' : 'Guardar proyecto' ?></button>
                    <a href="<?= e(url('admin/proyectos')) ?>" class="btn btn--ghost">Cancelar</a>
                </div>
            </form>
