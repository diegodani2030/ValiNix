<?php
/**
 * Layout del panel administrativo: menú lateral + contenido.
 *
 * @var string                    $content
 * @var string                    $title
 * @var array<string, mixed>      $user
 * @var bool                      $isAdmin
 * @var string                    $section
 * @var int                       $unread
 * @var array{status: ?string, message: ?string} $flash
 * @var bool                      $mustChange      Debe cambiar la contraseña antes de usar el panel.
 */

$menu = [
    ['key' => 'dashboard',   'label' => 'Inicio',      'href' => 'admin',             'admin' => false],
    ['key' => 'mensajes',    'label' => 'Mensajes',    'href' => 'admin/mensajes',    'admin' => true],
    ['key' => 'proyectos',   'label' => 'Proyectos',   'href' => 'admin/proyectos',   'admin' => false],
    ['key' => 'servicios',   'label' => 'Servicios',   'href' => 'admin/servicios',   'admin' => false],
    ['key' => 'testimonios', 'label' => 'Testimonios', 'href' => 'admin/testimonios', 'admin' => false],
    ['key' => 'usuarios',    'label' => 'Usuarios',    'href' => 'admin/usuarios',    'admin' => true],
    ['key' => 'cuenta',      'label' => 'Mi cuenta',   'href' => 'admin/cuenta',      'admin' => false],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> — Panel ValiNix</title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/icons/favicon.svg')) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin">

<a href="#admin-main" class="skip-link">Saltar al contenido</a>

<div class="admin-shell">
    <aside class="admin-sidebar" aria-label="Menú del panel">
        <a href="<?= e(url('admin')) ?>" class="navbar__logo admin-sidebar__logo">
            <span class="navbar__logo-mark"><?= icon('logo') ?></span>
            <span class="navbar__logo-text">valinix</span>
        </a>

        <nav class="admin-nav">
<?php foreach ($menu as $item): ?>
<?php if ($item['admin'] && !$isAdmin) { continue; } ?>
            <a href="<?= e(url($item['href'])) ?>"<?= $section === $item['key'] ? ' class="is-active" aria-current="page"' : '' ?>>
                <?= e($item['label']) ?>
<?php if ($item['key'] === 'mensajes' && $unread > 0): ?>
                <span class="admin-badge" title="Mensajes sin leer"><?= (int) $unread ?></span>
<?php endif; ?>
            </a>
<?php endforeach; ?>
        </nav>

        <div class="admin-sidebar__footer">
            <a href="<?= e(url('/')) ?>" class="admin-sidebar__site" target="_blank" rel="noopener">Ver sitio web ↗</a>
        </div>
    </aside>

    <div class="admin-content">
        <header class="admin-topbar">
            <h1 class="admin-topbar__title"><?= e($title) ?></h1>
            <div class="admin-user">
                <span class="admin-user__info">
                    <strong><?= e($user['nombre'] ?? '') ?></strong>
                    <span class="admin-role admin-role--<?= $isAdmin ? 'admin' : 'user' ?>"><?= e($user['rol'] ?? '') ?></span>
                </span>
                <form action="<?= e(url('logout')) ?>" method="POST">
                    <?= csrf_field() ?>

                    <button type="submit" class="btn btn--ghost btn--sm">Cerrar sesión</button>
                </form>
            </div>
        </header>

        <main id="admin-main" class="admin-main">
<?php if ($mustChange): ?>
            <div class="admin-alert admin-alert--warning">
                Por seguridad, elige una contraseña nueva (la actual la asignó el sistema o un administrador). Hasta entonces solo puedes usar <a href="<?= e(url('admin/cuenta')) ?>">Mi cuenta</a>.
            </div>
<?php endif; ?>
<?php if (!empty($flash['message'])): ?>
            <div class="admin-alert admin-alert--<?= $flash['status'] === 'error' ? 'error' : 'success' ?>" role="status"><?= e($flash['message']) ?></div>
<?php endif; ?>
<?= $content ?>
        </main>
    </div>
</div>

<script src="<?= e(asset('js/admin.js')) ?>"></script>
</body>
</html>
