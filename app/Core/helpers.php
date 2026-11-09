<?php
/**
 * Funciones de ayuda disponibles en controladores y vistas.
 */

declare(strict_types=1);

use App\Core\Config;

/** Escapa un valor para imprimirlo de forma segura en HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Lee un valor de config/config.php usando notación con puntos: config('contact.email'). */
function config(string $key, $default = null)
{
    return Config::get($key, $default);
}

/** Carpeta base del sitio ('' si está en la raíz del dominio, '/sub' si está en una subcarpeta). */
function base_url(): string
{
    static $base = null;

    if ($base === null) {
        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $base   = rtrim(str_replace('\\', '/', dirname($script)), '/');
    }

    return $base;
}

/** URL interna del sitio: url('contacto') -> /contacto */
function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

/**
 * URL de un archivo estático con su versión: asset('css/style.css') -> /css/style.css?v=1731110000
 * La versión es la fecha de modificación del archivo, así el navegador
 * descarga la versión nueva cada vez que cambia (y usa la caché mientras no cambie).
 */
function asset(string $path): string
{
    $file = BASE_PATH . '/' . ltrim($path, '/');

    return url($path) . (is_file($file) ? '?v=' . filemtime($file) : '');
}

/** Enlace de WhatsApp con el número configurado. */
function whatsapp_url(): string
{
    return 'https://wa.me/' . preg_replace('/\D+/', '', (string) config('contact.whatsapp_number', ''));
}

/** Devuelve el SVG de un icono definido en app/Views/partials/icons.php. */
function icon(string $name): string
{
    static $icons = null;

    if ($icons === null) {
        $icons = require APP_PATH . '/Views/partials/icons.php';
    }

    return $icons[$name] ?? '';
}

/** Mensaje de error de un campo del formulario (vacío si no tiene error). @param array<string, string> $errors */
function field_error(array $errors, string $field): string
{
    if (!isset($errors[$field])) {
        return '';
    }

    return '<span class="field-error" id="error-' . e($field) . '">' . e($errors[$field]) . '</span>';
}

/** Atributos para marcar un campo con error (lectores de pantalla y estilos). @param array<string, string> $errors */
function invalid_attr(array $errors, string $field): string
{
    return isset($errors[$field]) ? ' aria-invalid="true" aria-describedby="error-' . e($field) . '"' : '';
}

/** Campo oculto con el token CSRF para los formularios POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(\App\Core\Session::csrfToken()) . '">';
}
