<?php /** @var array<int, array<string, string>> $faqs */ ?>
    <section class="faq" id="faq">
        <div class="container">
            <div class="faq__list">
<?php foreach ($faqs as $faq): ?>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span><?= e($faq['q']) ?></span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p><?= e($faq['a']) ?></p>
                    </div>
                </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

