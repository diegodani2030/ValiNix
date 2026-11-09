<?php
/**
 * Encabezado de las páginas internas: miga de pan, título (h1) y descripción.
 *
 * @var array<string, string> $page Datos de la página (eyebrow, heading, lead, nav).
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
            <h1 class="page-header__title"><?= e($page['heading']) ?></h1>
<?php if (!empty($page['lead'])): ?>
            <p class="page-header__lead"><?= e($page['lead']) ?></p>
<?php endif; ?>
        </div>
    </section>

