<?php /** @var array<string, array<string, string>> $explore Páginas a destacar (SiteContent::explorePages()). */ ?>
    <section class="explore" id="explora">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Explora</span>
                <h2 class="section-title">Todo lo que necesitas saber, en un clic.</h2>
            </div>

            <div class="services__grid">
<?php foreach ($explore as $item): ?>
                <a href="<?= e(url($item['path'])) ?>" class="service-card explore-card reveal">
                    <span class="service-card__icon"><?= icon($item['icon']) ?></span>
                    <h3><?= e($item['nav']) ?></h3>
                    <p><?= e($item['summary']) ?></p>
                    <span class="project-card__link"><?= e($item['cta']) ?> →</span>
                </a>
<?php endforeach; ?>
            </div>
        </div>
    </section>

