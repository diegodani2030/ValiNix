<?php /** @var string[] $benefits */ ?>
    <section class="benefits" id="beneficios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Por qué ValiNix</span>
                <h2 class="section-title">Cada sitio incluye lo que realmente importa.</h2>
            </div>

            <div class="benefits__grid">
<?php foreach ($benefits as $benefit): ?>
                <div class="benefit reveal"><span class="benefit__dot"></span><?= e($benefit) ?></div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

