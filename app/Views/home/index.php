<?php
/**
 * Vista de la página principal. Cada sección está en su propio archivo
 * dentro de home/sections/ y usa los datos que manda HomeController.
 */
?>
<main id="main">

<?php
require __DIR__ . '/sections/hero.php';
require __DIR__ . '/sections/problem.php';
require __DIR__ . '/sections/services.php';
require __DIR__ . '/sections/benefits.php';
require __DIR__ . '/sections/process.php';
require __DIR__ . '/sections/portfolio.php';
require __DIR__ . '/sections/pricing.php';
require __DIR__ . '/sections/testimonials.php';
require __DIR__ . '/sections/faq.php';
require __DIR__ . '/sections/cta.php';
require __DIR__ . '/sections/contact.php';
?>

</main>
