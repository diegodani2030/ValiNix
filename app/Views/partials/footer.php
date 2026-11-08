<?php
/** @var string $navBase Prefijo de los enlaces ('' en la home, '/' en otras páginas). */

$quickLinks = [
    'inicio'    => 'Inicio',
    'servicios' => 'Servicios',
    'proceso'   => 'Proceso',
    'proyectos' => 'Proyectos',
    'precios'   => 'Precios',
];
$serviceLinks = ['Diseño web', 'Landing pages', 'Sitios empresariales', 'Catálogos digitales', 'SEO y rendimiento'];
?>
<footer class="footer">
    <div class="container footer__inner">
        <div class="footer__brand">
            <a href="<?= e($navBase) ?>#inicio" class="navbar__logo" aria-label="ValiNix - inicio">
                <span class="navbar__logo-mark"><?= str_replace(['width="28"', 'height="28"'], ['width="26"', 'height="26"'], icon('logo')) ?></span>
                <span class="navbar__logo-text">valinix</span>
            </a>
            <p class="footer__desc">Diseñamos y desarrollamos páginas web a la medida de tu negocio: rápidas, modernas y pensadas para convertir visitantes en clientes.</p>
            <div class="footer__social" aria-label="Redes sociales">
                <a href="#" aria-label="Instagram" class="footer__social-link"><?= icon('social-instagram') ?></a>
                <a href="#" aria-label="LinkedIn" class="footer__social-link"><?= icon('social-linkedin') ?></a>
                <a href="<?= e(whatsapp_url()) ?>" aria-label="WhatsApp" class="footer__social-link"><?= icon('social-whatsapp') ?></a>
            </div>
        </div>

        <div class="footer__col">
            <h3 class="footer__title">Enlaces rápidos</h3>
            <ul class="footer__links">
<?php foreach ($quickLinks as $anchor => $label): ?>
                <li><a href="<?= e($navBase) ?>#<?= e($anchor) ?>"><?= e($label) ?></a></li>
<?php endforeach; ?>
            </ul>
        </div>

        <div class="footer__col">
            <h3 class="footer__title">Servicios</h3>
            <ul class="footer__links">
<?php foreach ($serviceLinks as $label): ?>
                <li><a href="<?= e($navBase) ?>#servicios"><?= e($label) ?></a></li>
<?php endforeach; ?>
            </ul>
        </div>

        <div class="footer__col">
            <h3 class="footer__title">Contacto</h3>
            <ul class="footer__links">
                <li><a href="mailto:<?= e(config('contact.email')) ?>"><?= e(config('contact.email')) ?></a></li>
                <li><a href="<?= e(whatsapp_url()) ?>">WhatsApp: <?= e(config('contact.whatsapp_display')) ?></a></li>
                <li><?= e(config('contact.hours_short')) ?></li>
            </ul>
        </div>
    </div>

    <div class="footer__bottom">
        <div class="container footer__bottom-inner">
            <p>&copy; <?= date('Y') ?> ValiNix. Todos los derechos reservados.</p>
            <p class="footer__bottom-tag">Diseñado y desarrollado por ValiNix</p>
        </div>
    </div>
</footer>

<button id="backToTop" class="back-to-top" aria-label="Volver arriba"><?= icon('arrow-up') ?></button>
