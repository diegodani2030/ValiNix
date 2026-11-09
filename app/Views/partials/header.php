<?php
/**
 * @var array<string, array<string, string>> $navPages Páginas del menú (SiteContent::pages()).
 * @var array<string, string>                $page     Página actual.
 */

$currentKey = $page['key'] ?? '';
?>
<header class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="<?= e(url('/')) ?>" class="navbar__logo" aria-label="ValiNix - inicio">
            <span class="navbar__logo-mark"><?= icon('logo') ?></span>
            <span class="navbar__logo-text">valinix</span>
        </a>

        <nav class="navbar__nav" id="navbarNav" aria-label="Navegación principal">
            <ul class="navbar__list">
<?php foreach ($navPages as $key => $navPage): ?>
                <li><a href="<?= e(url($navPage['path'])) ?>"<?= $key === $currentKey ? ' class="is-active" aria-current="page"' : '' ?>><?= e($navPage['nav']) ?></a></li>
<?php endforeach; ?>
            </ul>
        </nav>

        <div class="navbar__actions">
<?php if (\App\Core\Auth::hasSession()): ?>
            <a href="<?= e(url('admin')) ?>" class="btn btn--ghost btn--sm navbar__login">Mi panel</a>
<?php else: ?>
            <a href="<?= e(url('login')) ?>" class="btn btn--ghost btn--sm navbar__login"<?= $currentKey === 'login' ? ' aria-current="page"' : '' ?>>Iniciar sesión</a>
<?php endif; ?>
            <a href="<?= e(url('contacto')) ?>" class="btn btn--accent btn--sm navbar__cta">Solicitar cotización</a>
            <button class="navbar__toggle" id="navToggle" aria-expanded="false" aria-controls="navbarNav" aria-label="Abrir menú">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
