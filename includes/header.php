<?php

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ValiNix — Diseño y desarrollo web para empresas y emprendedores</title>
    <meta name="description" content="En ValiNix creamos páginas web modernas, rápidas y enfocadas en resultados: landing pages, sitios empresariales y catálogos digitales con integración a WhatsApp.">
    <meta name="keywords" content="diseño web, desarrollo web, landing pages, páginas web para empresas, páginas web Colombia">
    <meta name="author" content="ValiNix">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="ValiNix — Transformamos ideas en páginas web que generan resultados">
    <meta property="og:description" content="Diseño y desarrollo de páginas web, landing pages y sitios empresariales enfocados en convertir visitantes en clientes.">
    <meta property="og:image" content="/assets/images/og-cover.jpg">
    <meta property="og:locale" content="es_CO">

    <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<a href="#main" class="skip-link">Saltar al contenido</a>

<header class="navbar" id="navbar">
    <div class="container navbar__inner">
        <a href="#inicio" class="navbar__logo" aria-label="ValiNix - inicio">
            <span class="navbar__logo-mark">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 3L4 25" stroke="var(--accent)" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M4 14L14 3" stroke="var(--accent)" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M8.5 10.5L18 25" stroke="#F2F4F8" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="navbar__logo-text">valinix</span>
        </a>

        <nav class="navbar__nav" id="navbarNav" aria-label="Navegación principal">
            <ul class="navbar__list">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#proceso">Proceso</a></li>
                <li><a href="#proyectos">Proyectos</a></li>
                <li><a href="#precios">Precios</a></li>
                <li><a href="#faq">Preguntas frecuentes</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
        </nav>

        <div class="navbar__actions">
            <a href="#contacto" class="btn btn--accent btn--sm navbar__cta">Solicitar cotización</a>
            <button class="navbar__toggle" id="navToggle" aria-expanded="false" aria-controls="navbarNav" aria-label="Abrir menú">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>
