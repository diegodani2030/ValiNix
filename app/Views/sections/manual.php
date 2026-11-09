<?php /** @var string $pdfUrl URL del manual en PDF. */ ?>
    <section class="manual">
        <div class="container">
            <div class="manual__frame reveal">
                <iframe src="<?= e($pdfUrl) ?>#view=FitH" title="Manual de usuario de ValiNix" loading="lazy"></iframe>
            </div>
            <p class="manual__fallback">Si tu navegador no muestra el documento, <a href="<?= e($pdfUrl) ?>" download="Manual_de_Usuario_ValiNix.pdf">descárgalo aquí</a>.</p>
        </div>
    </section>

