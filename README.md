# ValiNix

Landing page de ValiNix (diseño y desarrollo web), hecha en PHP sin frameworks
con el patrón **MVC (Modelo – Vista – Controlador)**.

Sitio publicado: https://valinix.infinityfree.me

## Estructura

```
index.php                  Front controller: toda petición entra por aquí
.htaccess                  Manda las URLs a index.php y bloquea app/ y config/
config/config.php          Datos del sitio: correo, WhatsApp, horario, textos SEO

app/
├── bootstrap.php          Autoload de clases, configuración y sesión
├── routes.php             Rutas: URL -> Controlador@método
├── Core/                  "Mini framework"
│   ├── Router.php         Busca la ruta y llama al controlador
│   ├── Controller.php     Base de los controladores (view, redirect)
│   ├── View.php           Renderiza vistas dentro del layout
│   ├── Session.php        Sesión, mensajes flash y token CSRF
│   ├── Config.php         Lee config/config.php
│   └── helpers.php        e(), url(), asset(), config(), icon(), whatsapp_url()
├── Models/                M — datos y reglas de negocio
│   ├── SiteContent.php    Servicios, proyectos, planes, testimonios, FAQ...
│   └── ContactRequest.php Limpia, valida y envía la solicitud de cotización
├── Controllers/           C — reciben la petición y deciden qué responder
│   ├── HomeController.php     GET  /          -> página principal
│   ├── ContactController.php  POST /contacto  -> procesa el formulario
│   └── ErrorController.php    404 / 405
└── Views/                 V — solo HTML, sin lógica de negocio
    ├── layouts/main.php   <head>, navbar, footer
    ├── partials/          header, footer, iconos SVG
    ├── home/index.php     Une las secciones de la home
    ├── home/sections/     hero, servicios, proyectos, precios, contacto...
    └── errors/404.php

css/  js/  assets/         Archivos estáticos (se sirven directamente)
tests/smoke.sh             Prueba automática del sitio y del formulario
```

## Cómo funciona una petición

1. El navegador pide `/` → `.htaccess` la manda a `index.php`.
2. `index.php` carga `app/bootstrap.php` y le pasa la URL al `Router`.
3. El `Router` encuentra la ruta en `app/routes.php` → `HomeController@index`.
4. El controlador pide los datos al modelo `SiteContent` y llama a la vista `home/index`.
5. La vista se renderiza dentro de `layouts/main.php` y se envía al navegador.

El formulario hace `POST /contacto` → `ContactController@store` usa el modelo
`ContactRequest` para validar y enviar el correo, guarda un mensaje flash en la
sesión y redirige a `/#contacto` (patrón POST → redirección → GET).

## Cambios comunes

| Quiero cambiar…                         | Archivo                                   |
|-----------------------------------------|-------------------------------------------|
| Correo, WhatsApp u horario              | `config/config.php`                       |
| Servicios, proyectos, planes, FAQ       | `app/Models/SiteContent.php`              |
| Diseño/HTML de una sección              | `app/Views/home/sections/<sección>.php`   |
| Menú o footer                           | `app/Views/partials/header.php` / `footer.php` |
| Reglas de validación del formulario     | `app/Models/ContactRequest.php`           |
| Agregar una página nueva                | ruta en `app/routes.php` + método en un controlador + vista en `app/Views/` |

## Probar en local

Requiere PHP 7.4 o superior.

```bash
php -S localhost:8000 index.php
```

Abre http://localhost:8000. Para correr las pruebas (con el servidor encendido):

```bash
bash tests/smoke.sh http://localhost:8000
```

## Despliegue

El workflow `.github/workflows/deploy-infinityfree.yml`:

- En cualquier rama o pull request: revisa la sintaxis y corre `tests/smoke.sh` con PHP 7.4 y 8.3.
- En `main`: si las pruebas pasan, sube el sitio por FTP a `/htdocs/` de InfinityFree.

Usa los secretos del repositorio `FTP_SERVER`, `FTP_USERNAME` y `FTP_PASSWORD`.
