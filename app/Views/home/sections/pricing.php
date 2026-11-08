<?php /** @var array<int, array<string, mixed>> $plans */ ?>
    <section class="pricing" id="precios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Planes</span>
                <h2 class="section-title">Un plan para cada etapa de tu negocio.</h2>
            </div>

            <div class="pricing__grid">
<?php foreach ($plans as $plan): ?>
                <div class="price-card<?= $plan['featured'] ? ' price-card--featured' : '' ?> reveal">
<?php if (!empty($plan['badge'])): ?>
                    <span class="price-card__badge"><?= e($plan['badge']) ?></span>
<?php endif; ?>
                    <h3 class="price-card__name"><?= e($plan['name']) ?></h3>
                    <p class="price-card__desc"><?= e($plan['desc']) ?></p>
                    <p class="price-card__price"><?= e($plan['price']) ?></p>
                    <ul class="price-card__features">
<?php foreach ($plan['features'] as $feature): ?>
                        <li><?= e($feature) ?></li>
<?php endforeach; ?>
                    </ul>
                    <a href="#contacto" class="btn <?= $plan['featured'] ? 'btn--accent' : 'btn--ghost' ?> btn--block">Solicitar cotización</a>
                </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

