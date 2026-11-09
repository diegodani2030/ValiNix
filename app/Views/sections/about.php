<?php
/**
 * Identidad de ValiNix: misión, visión, valores y enlace al manual.
 *
 * @var array{mission: string, vision: string, values: array<int, array<string, string>>} $about
 */
?>
    <section class="identidad" id="identidad">
        <div class="container">
            <div class="identidad__top reveal">
                <span class="identidad__logo" aria-hidden="true"><?= str_replace(['width="28"', 'height="28"'], ['width="72"', 'height="72"'], icon('logo')) ?></span>
                <div>
                    <span class="eyebrow">Nuestra identidad</span>
                    <h2 class="section-title">Misión, visión y <span class="text-accent">valores</span></h2>
                </div>
            </div>

            <div class="identidad__grid">
                <article class="identidad-card reveal">
                    <span class="identidad-card__icon" aria-hidden="true"><?= icon('mission') ?></span>
                    <h3>Misión</h3>
                    <p><?= e($about['mission']) ?></p>
                </article>

                <article class="identidad-card identidad-card--vision reveal">
                    <span class="identidad-card__icon" aria-hidden="true"><?= icon('vision') ?></span>
                    <h3>Visión</h3>
                    <p><?= e($about['vision']) ?></p>
                </article>
            </div>

            <h3 class="identidad__valores-title reveal">Nuestros valores</h3>
            <div class="valores-grid">
<?php foreach ($about['values'] as $i => $value): ?>
                <article class="valor-card reveal">
                    <span class="valor-card__num"><?= sprintf('%02d', $i + 1) ?></span>
                    <h4><?= e($value['title']) ?></h4>
                    <p><?= e($value['text']) ?></p>
                </article>
<?php endforeach; ?>
            </div>

            <div class="identidad__manual reveal">
                <span class="identidad__manual-icon" aria-hidden="true"><?= icon('document') ?></span>
                <p>¿Primera vez en ValiNix? Consulta o descarga la guía paso a paso del sistema.</p>
                <a href="<?= e(url('manual')) ?>" class="btn btn--ghost">Ver manual de usuario</a>
            </div>
        </div>
    </section>

