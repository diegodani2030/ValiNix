<?php
/**
 * @var array{status: ?string, message: ?string, old: array<string, string>, token: string} $form
 * @var array<int, string>    $serviceOptions Servicios (id => título), de la tabla servicios.
 * @var array<string, string> $planOptions    Planes (clave => nombre), de la tabla planes.
 */

$old = is_array($form['old']) ? $form['old'] : [];
$val = static function (string $field) use ($old): string {
    return e($old[$field] ?? '');
};
?>
    <section class="contact" id="contacto">
        <div class="container contact__inner">
            <div class="contact__info reveal">
                <span class="eyebrow">Otros canales</span>
                <p class="contact__lead">¿Prefieres hablar directamente? Escríbenos por WhatsApp o al correo, en nuestro horario de atención.</p>

                <ul class="contact__details">
                    <li>
                        <?= icon('contact-whatsapp') ?>

                        <a href="<?= e(whatsapp_url()) ?>"><?= e(config('contact.whatsapp_display')) ?></a>
                    </li>
                    <li>
                        <?= icon('contact-mail') ?>

                        <a href="mailto:<?= e(config('contact.email')) ?>"><?= e(config('contact.email')) ?></a>
                    </li>
                    <li>
                        <?= icon('contact-clock') ?>

                        <span><?= e(config('contact.hours')) ?></span>
                    </li>
                </ul>
            </div>

            <form class="contact__form reveal" id="contactForm" action="<?= e(url('contacto')) ?>" method="POST" novalidate>
<?php if ($form['status'] === 'success'): ?>
                <div class="form-alert form-alert--success"><?= e($form['message']) ?></div>
<?php elseif ($form['status'] === 'error'): ?>
                <div class="form-alert form-alert--error"><?= e($form['message']) ?></div>
<?php endif; ?>

                <input type="hidden" name="_token" value="<?= e($form['token']) ?>">

                <!-- Honeypot field (hidden from real users) -->
                <div class="hp-field" aria-hidden="true">
                    <label for="website">No llenar este campo</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required minlength="2" maxlength="100" value="<?= $val('nombre') ?>">
                        <span class="form-error" data-error-for="nombre"></span>
                    </div>
                    <div class="form-group">
                        <label for="empresa">Empresa</label>
                        <input type="text" id="empresa" name="empresa" maxlength="120" value="<?= $val('empresa') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="correo">Correo electrónico *</label>
                        <input type="email" id="correo" name="correo" required maxlength="150" value="<?= $val('correo') ?>">
                        <span class="form-error" data-error-for="correo"></span>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" required maxlength="20" value="<?= $val('telefono') ?>">
                        <span class="form-error" data-error-for="telefono"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="asunto">Asunto *</label>
                    <input type="text" id="asunto" name="asunto" required minlength="3" maxlength="150" value="<?= $val('asunto') ?>">
                    <span class="form-error" data-error-for="asunto"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="servicio">Servicio de interés</label>
                        <select id="servicio" name="servicio">
                            <option value="">Selecciona un servicio</option>
<?php foreach ($serviceOptions as $value => $label): ?>
                            <option value="<?= (int) $value ?>"<?= ($old['servicio'] ?? null) === (string) $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="plan">Plan de interés</label>
                        <select id="plan" name="plan">
<?php foreach ($planOptions as $value => $label): ?>
                            <option value="<?= e($value) ?>"<?= ($old['plan'] ?? null) === (string) $value ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje *</label>
                    <textarea id="mensaje" name="mensaje" rows="4" required minlength="10" maxlength="3000"><?= $val('mensaje') ?></textarea>
                    <span class="form-error" data-error-for="mensaje"></span>
                </div>

                <button type="submit" class="btn btn--accent btn--block">Enviar solicitud</button>
            </form>
        </div>
    </section>

