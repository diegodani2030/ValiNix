<?php
/**
 * Layout principal: <head>, navbar, contenido de la vista y footer.
 *
 * @var string      $content   HTML de la vista renderizada.
 * @var string|null $pageTitle Título de la pestaña (opcional).
 * @var bool|null   $noindex   true para que Google no indexe la página.
 * @var string|null $navBase   Prefijo de los enlaces del menú ('' en la home).
 */

$pageTitle = $pageTitle ?? config('meta.title');
$navBase   = $navBase ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e(config('meta.description')) ?>">
    <meta name="keywords" content="<?= e(config('meta.keywords')) ?>">
    <meta name="author" content="<?= e(config('app.name')) ?>">
<?php if (!empty($noindex)): ?>
    <meta name="robots" content="noindex">
<?php endif; ?>

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e(config('meta.og_title')) ?>">
    <meta property="og:description" content="<?= e(config('meta.og_description')) ?>">
    <meta property="og:image" content="<?= e(config('meta.og_image')) ?>">
    <meta property="og:locale" content="es_CO">

    <link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/icons/favicon.svg')) ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>

<a href="#main" class="skip-link">Saltar al contenido</a>

<?php require APP_PATH . '/Views/partials/header.php'; ?>

<?= $content ?>

<?php require APP_PATH . '/Views/partials/footer.php'; ?>

<script src="<?= e(asset('js/script.js')) ?>"></script>
</body>
</html>
