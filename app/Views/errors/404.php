<?php /** Página de error 404 (usa el layout principal). */ ?>
<main id="main">
    <section class="cta-final">
        <div class="container cta-final__inner">
            <span class="eyebrow">Error 404</span>
            <h2>Esta página no existe.</h2>
            <p>Puede que el enlace esté mal escrito o que la página se haya movido.</p>
            <div class="cta-final__actions">
                <a href="<?= e(url('/')) ?>" class="btn btn--accent">Volver al inicio</a>
                <a href="<?= e(url('/')) ?>#contacto" class="btn btn--ghost">Contactarnos</a>
            </div>
        </div>
    </section>
</main>
