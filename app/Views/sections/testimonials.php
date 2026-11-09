<?php /** @var array<int, array<string, mixed>> $testimonials */ ?>
    <section class="testimonials" id="testimonios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Lo que dicen nuestros clientes</span>
                <h2 class="section-title">Contenido demostrativo de clientes ficticios.</h2>
            </div>

            <div class="testimonial-slider" id="testimonialSlider">
                <div class="testimonial-track" id="testimonialTrack">
<?php foreach ($testimonials as $i => $testimonial): ?>
<?php $rating = max(0, min(5, (int) $testimonial['rating'])); ?>
                    <div class="testimonial-slide<?= $i === 0 ? ' is-active' : '' ?>">
                        <div class="testimonial-stars" aria-label="<?= $rating ?> de 5 estrellas"><?= str_repeat('★', $rating) . str_repeat('☆', 5 - $rating) ?></div>
                        <p class="testimonial-quote"><?= e($testimonial['quote']) ?></p>
                        <p class="testimonial-author"><?= e($testimonial['author']) ?> <span>— <?= e($testimonial['company']) ?></span></p>
                    </div>
<?php endforeach; ?>
                </div>

                <div class="testimonial-controls">
                    <button class="testimonial-btn" id="testimonialPrev" aria-label="Testimonio anterior">‹</button>
                    <div class="testimonial-dots" id="testimonialDots"></div>
                    <button class="testimonial-btn" id="testimonialNext" aria-label="Testimonio siguiente">›</button>
                </div>
            </div>
        </div>
    </section>

