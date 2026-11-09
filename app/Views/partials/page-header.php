<?php
/**
 * Encabezado de las páginas internas: miga de pan, título (h1), descripción y botones opcionales.
 *
 * @var array<string, string>                   $page          Datos de la página (eyebrow, heading, heading_accent, lead, nav).
 * @var array<int, array<string, mixed>>|null   $headerActions Botones bajo el título (label, href, variant, download, newTab).
 */
?>
    <section class="page-header">
        <div class="hero__glow" aria-hidden="true"></div>
        <div class="container">
            <nav class="breadcrumb" aria-label="Ruta de navegación">
                <a href="<?= e(url('/')) ?>">Inicio</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?= e($page['nav']) ?></span>
            </nav>
            <span class="eyebrow"><?= e($page['eyebrow']) ?></span>
            <h1 class="page-header__title"><?= e($page['heading']) ?><?php if (!empty($page['heading_accent'])): ?> <span class="text-accent"><?= e($page['heading_accent']) ?></span><?php endif; ?></h1>
<?php if (!empty($page['lead'])): ?>
            <p class="page-header__lead"><?= e($page['lead']) ?></p>
<?php endif; ?>
<?php if (!empty($headerActions)): ?>
            <div class="hero__actions">
<?php foreach ($headerActions as $action): ?>
                <a href="<?= e($action['href']) ?>" class="btn btn--<?= e($action['variant'] ?? 'ghost') ?>"<?= !empty($action['download']) ? ' download="' . e($action['download']) . '"' : '' ?><?= !empty($action['newTab']) ? ' target="_blank" rel="noopener"' : '' ?>><?= e($action['label']) ?></a>
<?php endforeach; ?>
            </div>
<?php endif; ?>
        </div>
    </section>

