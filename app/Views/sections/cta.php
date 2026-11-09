<?php
/**
 * Llamado a la acción final. Una página puede personalizarlo pasando $cta
 * (title, text, primary, secondary); si no, usa los textos por defecto.
 *
 * @var array<string, mixed>|null $cta
 */
$cta = array_merge([
    'title'     => '¿Listo para llevar tu negocio a internet?',
    'text'      => 'Cuéntanos qué necesitas y creemos juntos una presencia digital profesional para tu negocio.',
    'primary'   => ['label' => 'Solicitar cotización', 'href' => url('contacto')],
    'secondary' => ['label' => 'Hablar por WhatsApp', 'href' => whatsapp_url(), 'newTab' => true],
], $cta ?? []);
?>
    <section class="cta-final">
        <div class="container cta-final__inner reveal">
            <h2><?= e($cta['title']) ?></h2>
            <p><?= e($cta['text']) ?></p>
            <div class="cta-final__actions">
                <a href="<?= e($cta['primary']['href']) ?>" class="btn btn--accent"><?= e($cta['primary']['label']) ?></a>
                <a href="<?= e($cta['secondary']['href']) ?>"<?= !empty($cta['secondary']['newTab']) ? ' target="_blank" rel="noopener"' : '' ?> class="btn btn--ghost"><?= e($cta['secondary']['label']) ?></a>
            </div>
        </div>
    </section>

