<?php
/**
 * Configuración general del sitio.
 * Cambia aquí los datos de contacto en lugar de buscarlos por las vistas.
 */

return [
    'app' => [
        'name'     => 'ValiNix',
        'debug'    => false, // true solo en local: muestra los errores de PHP en pantalla
        'timezone' => 'America/Bogota',
    ],

    'meta' => [
        'title'       => 'ValiNix — Diseño y desarrollo web para empresas y emprendedores',
        'description' => 'En ValiNix creamos páginas web modernas, rápidas y enfocadas en resultados: landing pages, sitios empresariales y catálogos digitales con integración a WhatsApp.',
        'keywords'    => 'diseño web, desarrollo web, landing pages, páginas web para empresas, páginas web Colombia',
        'og_title'       => 'ValiNix — Transformamos ideas en páginas web que generan resultados',
        'og_description' => 'Diseño y desarrollo de páginas web, landing pages y sitios empresariales enfocados en convertir visitantes en clientes.',
        'og_image'       => '/assets/images/og-cover.jpg',
    ],

    /*
     * Base de datos MySQL. Los datos reales NO se escriben aquí:
     *  - En InfinityFree, el deploy crea config/database.local.php con los
     *    secretos DB_HOST, DB_NAME, DB_USER y DB_PASSWORD de GitHub.
     *  - En local, crea tú mismo config/database.local.php (ver database.example.php)
     *    o define las variables de entorno DB_HOST, DB_NAME, DB_USER, DB_PASSWORD.
     * Si no hay base de datos, las páginas públicas siguen funcionando con el
     * contenido de respaldo de SiteContent, pero el panel no se puede usar.
     */
    'db' => array_merge(
        [
            'host'     => (string) getenv('DB_HOST'),
            'port'     => (int) (getenv('DB_PORT') ?: 3306),
            'name'     => (string) getenv('DB_NAME'),
            'user'     => (string) getenv('DB_USER'),
            'password' => (string) getenv('DB_PASSWORD'),
        ],
        is_file(__DIR__ . '/database.local.php') ? (array) require __DIR__ . '/database.local.php' : []
    ),

    'contact' => [
        'email'            => 'hola@valinix.software',      // a dónde llegan las solicitudes
        'from'             => 'no-reply@valinix.software',  // remitente de los correos
        'whatsapp_number'  => '573000000000',               // solo dígitos, con indicativo
        'whatsapp_display' => '+57 300 000 0000',
        'hours'            => 'Lunes a viernes · 8:00 a.m. – 6:00 p.m.',
        'hours_short'      => 'Lun – Vie · 8:00 a.m. – 6:00 p.m.',
    ],
];
