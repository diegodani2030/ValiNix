<?php
/** @var string $navBase Prefijo de los enlaces ('' en la home, '/' en otras páginas). */

$navLinks = [
    'inicio'    => 'Inicio',
    'servicios' => 'Servicios',
    'proceso'   => 'Proceso',
    'proyectos' => 'Proyectos',
    'precios'   => 'Precios',
    'faq'       => 'Preguntas frecuentes',
    'contacto'  => 'Contacto',
];
?>
<header class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="<?= e($navBase) ?>#inicio" class="navbar__logo" aria-label="ValiNix - inicio">
            <span class="navbar__logo-mark"><?= icon('logo') ?></span>
            <span class="navbar__logo-text">valinix</span>
        </a>

        <nav class="navbar__nav" id="navbarNav" aria-label="Navegación principal">
            <ul class="navbar__list">
<?php foreach ($navLinks as $anchor => $label): ?>
                <li><a href="<?= e($navBase) ?>#<?= e($anchor) ?>"><?= e($label) ?></a></li>
<?php endforeach; ?>
            </ul>
        </nav>

        <div class="navbar__actions">
            <a href="<?= e($navBase) ?>#contacto" class="btn btn--accent btn--sm navbar__cta">Solicitar cotización</a>
            <button class="navbar__toggle" id="navToggle" aria-expanded="false" aria-controls="navbarNav" aria-label="Abrir menú">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
