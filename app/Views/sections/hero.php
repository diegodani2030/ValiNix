<?php /** @var array<int, array<string, mixed>> $stats */ ?>
    <section class="hero" id="inicio">
        <div class="hero__glow" aria-hidden="true"></div>
        <div class="container hero__inner">
            <div class="hero__content">
                <span class="eyebrow">Diseño &amp; desarrollo web</span>
                <h1 class="hero__title">Transformamos ideas en páginas web que <span class="text-accent">generan resultados.</span></h1>
                <p class="hero__text">Creamos sitios web modernos, rápidos y adaptables a cualquier dispositivo, pensados desde el primer clic para convertir visitantes en clientes.</p>
                <div class="hero__actions">
                    <a href="<?= e(url('contacto')) ?>" class="btn btn--accent">Quiero mi página web</a>
                    <a href="<?= e(url('proyectos')) ?>" class="btn btn--ghost">Ver proyectos</a>
                </div>

                <div class="hero__stats">
<?php foreach ($stats as $stat): ?>
                    <div class="hero__stat">
<?php if (isset($stat['target'])): ?>
                        <span class="hero__stat-number" data-counter data-target="<?= (int) $stat['target'] ?>">0</span><span class="text-accent"><?= e($stat['suffix']) ?></span>
<?php else: ?>
                        <span class="hero__stat-number hero__stat-number--text"><?= e($stat['text']) ?></span>
<?php endif; ?>
                        <span class="hero__stat-label"><?= e($stat['label']) ?></span>
                    </div>
<?php endforeach; ?>
                </div>
            </div>

            <div class="hero__visual" aria-hidden="true">
                <div class="code-window">
                    <div class="code-window__bar">
                        <span class="code-dot code-dot--red"></span>
                        <span class="code-dot code-dot--yellow"></span>
                        <span class="code-dot code-dot--green"></span>
                        <span class="code-window__file">index.php</span>
                    </div>
                    <pre class="code-window__body"><code id="typedCode"></code><span class="code-caret"></span></pre>
                </div>

                <div class="floating-card floating-card--1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3v18M3 12h18" stroke="var(--accent)" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Sitio publicado</span>
                </div>
                <div class="floating-card floating-card--2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Optimizado para SEO</span>
                </div>
                <div class="floating-card floating-card--3">
                    <span class="floating-card__pulse"></span>
                    <span>+128% tráfico</span>
                </div>
            </div>
        </div>
    </section>

