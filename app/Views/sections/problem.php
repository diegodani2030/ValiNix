<?php /** @var string[] $problems */ ?>
    <section class="problem" id="problema">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">El punto de partida</span>
                <h2 class="section-title">Tu negocio merece más que una página web.</h2>
            </div>

            <div class="problem__grid">
<?php foreach ($problems as $problem): ?>
                <div class="problem__item reveal">
                    <?= icon('problem-x') ?>

                    <p><?= e($problem) ?></p>
                </div>
<?php endforeach; ?>
            </div>

            <p class="problem__solution reveal">En ValiNix resolvemos esto: construimos tu presencia digital desde cero, con estrategia, diseño y código pensados para que tu negocio crezca.</p>
        </div>
    </section>

