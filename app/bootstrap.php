<?php
/**
 * Arranque de la aplicación: autoload de clases, configuración y sesión.
 */

declare(strict_types=1);

define('APP_PATH', BASE_PATH . '/app');

// Autoload sencillo (sin Composer): App\Core\Router -> app/Core/Router.php
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $file = APP_PATH . '/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require APP_PATH . '/Core/helpers.php';

\App\Core\Config::load(BASE_PATH . '/config/config.php');

error_reporting(E_ALL);
ini_set('display_errors', config('app.debug', false) ? '1' : '0');
date_default_timezone_set((string) config('app.timezone', 'America/Bogota'));

\App\Core\Session::start();

// El menú y el footer se arman con la lista de páginas del modelo.
\App\Core\View::share('navPages', \App\Models\SiteContent::menuPages());
\App\Core\View::share('allPages', \App\Models\SiteContent::pages());
