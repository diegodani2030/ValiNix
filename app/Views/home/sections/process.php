<?php /** @var array<int, array<string, string>> $steps */ ?>
    <section class="process" id="proceso">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Cómo trabajamos</span>
                <h2 class="section-title">Un proceso claro, de principio a fin.</h2>
            </div>

            <ol class="timeline">
<?php foreach ($steps as $i => $step): ?>
                <li class="timeline__step reveal">
                    <span class="timeline__num"><?= sprintf('%02d', $i + 1) ?></span>
                    <h3><?= e($step['title']) ?></h3>
                    <p><?= e($step['text']) ?></p>
                </li>
<?php endforeach; ?>
            </ol>
        </div>
    </section>

