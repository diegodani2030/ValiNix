<?php /** @var array<int, array<string, string>> $services */ ?>
    <section class="services" id="servicios">
        <div class="container">
            <div class="services__grid">
<?php foreach ($services as $service): ?>
                <div class="service-card reveal">
                    <span class="service-card__icon"><?= icon($service['icon']) ?></span>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['text']) ?></p>
                </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

