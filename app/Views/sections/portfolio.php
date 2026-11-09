<?php
/**
 * @var array<string, string>               $projectFilters
 * @var array<int, array<string, mixed>>    $projects
 */
?>
    <section class="portfolio" id="proyectos">
        <div class="container">
            <div class="portfolio__filters" role="tablist" aria-label="Filtrar proyectos">
<?php foreach ($projectFilters as $value => $label): ?>
                <button class="filter-btn<?= $value === 'all' ? ' is-active' : '' ?>" data-filter="<?= e($value) ?>"><?= e($label) ?></button>
<?php endforeach; ?>
            </div>

            <div class="portfolio__grid" id="portfolioGrid">
<?php foreach ($projects as $project): ?>
                <article class="project-card" data-category="<?= e($project['category']) ?>">
                    <div class="project-card__thumb project-card__thumb--<?= (int) $project['thumb'] ?>"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag"><?= e($project['tag']) ?></span>
                        <h3><?= e($project['title']) ?></h3>
                        <p><?= e($project['text']) ?></p>
                        <a href="<?= e($project['url']) ?>" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
<?php endforeach; ?>
            </div>
        </div>
    </section>

