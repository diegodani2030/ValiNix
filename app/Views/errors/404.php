<?php /** Página de error 404 (usa el layout principal). */ ?>
    <section class="page-header">
        <div class="hero__glow" aria-hidden="true"></div>
        <div class="container">
            <span class="eyebrow">Error 404</span>
            <h1 class="page-header__title">Esta página no existe.</h1>
            <p class="page-header__lead">Puede que el enlace esté mal escrito o que la página se haya movido.</p>
            <div class="hero__actions">
                <a href="<?= e(url('/')) ?>" class="btn btn--accent">Volver al inicio</a>
                <a href="<?= e(url('contacto')) ?>" class="btn btn--ghost">Contactarnos</a>
            </div>
        </div>
    </section>
