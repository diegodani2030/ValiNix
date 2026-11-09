<?php /** @var array<string, mixed> $mensaje */ ?>
            <div class="admin-toolbar">
                <a href="<?= e(url('admin/mensajes')) ?>" class="admin-link">← Volver a mensajes</a>
                <div class="admin-toolbar__group">
                    <form action="<?= e(url('admin/mensajes/estado')) ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $mensaje['id'] ?>">
                        <button type="submit" class="btn btn--ghost btn--sm"><?= (int) $mensaje['leido'] === 1 ? 'Marcar como no leído' : 'Marcar como leído' ?></button>
                    </form>
                    <form action="<?= e(url('admin/mensajes/eliminar')) ?>" method="POST" data-confirm="¿Eliminar este mensaje? Esta acción no se puede deshacer.">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int) $mensaje['id'] ?>">
                        <button type="submit" class="btn btn--danger btn--sm">Eliminar</button>
                    </form>
                </div>
            </div>

            <article class="admin-card admin-message">
                <h2 class="admin-card__title"><?= e($mensaje['asunto']) ?></h2>
                <dl class="admin-details">
                    <div><dt>Nombre</dt><dd><?= e($mensaje['nombre']) ?></dd></div>
                    <div><dt>Empresa</dt><dd><?= e($mensaje['empresa'] ?? '—') ?></dd></div>
                    <div><dt>Correo</dt><dd><a href="mailto:<?= e($mensaje['correo']) ?>"><?= e($mensaje['correo']) ?></a></dd></div>
                    <div><dt>Teléfono</dt><dd><?= e($mensaje['telefono']) ?></dd></div>
                    <div><dt>Servicio de interés</dt><dd><?= e($mensaje['servicio'] ?? '—') ?></dd></div>
                    <div><dt>Plan de interés</dt><dd><?= e($mensaje['plan'] ?? 'Sin plan') ?></dd></div>
                    <div><dt>Recibido</dt><dd><?= e(date('d/m/Y h:i a', (int) strtotime((string) $mensaje['creado_en']))) ?></dd></div>
                </dl>
                <div class="admin-message__body"><?= nl2br(e($mensaje['mensaje'])) ?></div>
            </article>
