<?php /** Panel sin conexión a la base de datos (respuesta 503, layout principal). */ ?>
    <section class="page-header">
        <div class="hero__glow" aria-hidden="true"></div>
        <div class="container">
            <span class="eyebrow">Error 503</span>
            <h1 class="page-header__title">El panel no está disponible.</h1>
            <p class="page-header__lead">No hay conexión con la base de datos en este momento. El sitio público sigue funcionando; intenta entrar al panel más tarde.</p>
            <div class="hero__actions">
                <a href="<?= e(url('/')) ?>" class="btn btn--accent">Volver al inicio</a>
            </div>
        </div>
    </section>
