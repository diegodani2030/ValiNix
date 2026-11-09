<?php
/**
 * Ejemplo de conexión a la base de datos.
 *
 * Copia este archivo como config/database.local.php y pon tus datos.
 * database.local.php NO se sube a GitHub (está en .gitignore).
 *
 * XAMPP (local):     host 127.0.0.1, usuario root, contraseña vacía.
 * InfinityFree:      lo crea el deploy automáticamente con los secretos de GitHub.
 */

return [
    'host'     => '127.0.0.1',
    'port'     => 3306,
    'name'     => 'valinix_db',
    'user'     => 'root',
    'password' => '',
];
