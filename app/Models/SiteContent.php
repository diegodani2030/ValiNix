<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Contenido de la landing page: servicios, proyectos, planes, testimonios, FAQ...
 *
 * Para cambiar textos o agregar elementos, edita estos arreglos; las vistas
 * los recorren automáticamente. Si algún día se usa una base de datos,
 * solo cambia este modelo y las vistas siguen igual.
 */
final class SiteContent
{
    /** @return array<int, array<string, mixed>> */
    public static function stats(): array
    {
        return [
            ['target' => 50, 'suffix' => '+', 'label' => 'Proyectos'],
            ['target' => 30, 'suffix' => '+', 'label' => 'Clientes'],
            ['target' => 99, 'suffix' => '%', 'label' => 'Satisfacción'],
            ['text' => '1:1', 'label' => 'Soporte personalizado'],
        ];
    }

    /** @return string[] */
    public static function problems(): array
    {
        return [
            'Tener una página desactualizada que no refleja lo que tu negocio es hoy.',
            'No verse bien —o directamente no funcionar— en celulares.',
            'Perder clientes porque no encuentran la información que buscan.',
            'No tener una presencia profesional que genere confianza en internet.',
            'Depender únicamente de redes sociales para vender.',
            'No tener un canal de contacto rápido y eficiente, como WhatsApp.',
        ];
    }

    /** @return array<int, array<string, string>> */
    public static function services(): array
    {
        return [
            ['icon' => 'service-web',       'title' => 'Diseño web',               'text' => 'Sitios modernos y profesionales adaptados a la identidad de cada empresa.'],
            ['icon' => 'service-landing',   'title' => 'Landing pages',            'text' => 'Páginas enfocadas en un solo objetivo: convertir visitantes en clientes.'],
            ['icon' => 'service-business',  'title' => 'Sitios empresariales',     'text' => 'Presencia digital completa para empresas y organizaciones.'],
            ['icon' => 'service-catalog',   'title' => 'Catálogos digitales',      'text' => 'Presenta tus productos y servicios de forma clara y profesional.'],
            ['icon' => 'whatsapp-outline',  'title' => 'Integración con WhatsApp', 'text' => 'Facilitamos que tus visitantes te contacten directamente desde la web.'],
            ['icon' => 'service-seo',       'title' => 'SEO y rendimiento',        'text' => 'Optimizamos velocidad, estructura y posicionamiento en buscadores.'],
        ];
    }

    /** @return string[] */
    public static function benefits(): array
    {
        return [
            'Diseño responsive',
            'Carga rápida',
            'Optimización SEO',
            'Código limpio',
            'Seguridad',
            'Integración con WhatsApp',
            'Formularios de contacto',
            'Diseño personalizado',
            'Soporte técnico',
        ];
    }

    /** @return array<int, array<string, string>> */
    public static function processSteps(): array
    {
        return [
            ['title' => 'Cuéntanos tu idea',          'text' => 'Nos compartes qué necesitas, cómo es tu negocio y qué quieres lograr con tu página.'],
            ['title' => 'Analizamos tus necesidades', 'text' => 'Definimos alcance, estructura de contenido y el plan que mejor se ajusta a tu proyecto.'],
            ['title' => 'Diseñamos tu página',        'text' => 'Creamos una propuesta visual alineada a tu marca antes de escribir una sola línea de código.'],
            ['title' => 'Desarrollamos y optimizamos', 'text' => 'Construimos el sitio, lo probamos en distintos dispositivos y lo optimizamos para velocidad y SEO.'],
            ['title' => 'Publicamos tu sitio',        'text' => 'Ponemos tu página en línea y te acompañamos con soporte una vez esté publicada.'],
        ];
    }

    /** Filtros del portafolio: valor => etiqueta. @return array<string, string> */
    public static function projectFilters(): array
    {
        return [
            'all'         => 'Todos',
            'landing'     => 'Landing Pages',
            'empresarial' => 'Empresariales',
            'ecommerce'   => 'E-commerce',
            'catalogo'    => 'Catálogos',
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function projects(): array
    {
        return [
            ['thumb' => 1, 'category' => 'landing',     'tag' => 'Landing Page',      'title' => 'Nova Studio',           'text' => 'Landing page para estudio de diseño de interiores enfocada en agendar visitas.', 'url' => '#'],
            ['thumb' => 2, 'category' => 'empresarial', 'tag' => 'Sitio Empresarial', 'title' => 'Andes Ingeniería',      'text' => 'Sitio corporativo con portafolio de proyectos y sección institucional completa.', 'url' => '#'],
            ['thumb' => 3, 'category' => 'ecommerce',   'tag' => 'E-commerce',        'title' => 'Verde Botánica',        'text' => 'Tienda en línea de plantas y accesorios con carrito de compras integrado.', 'url' => '#'],
            ['thumb' => 4, 'category' => 'catalogo',    'tag' => 'Catálogo Digital',  'title' => 'Muebles del Valle',     'text' => 'Catálogo digital de mobiliario con fichas técnicas y contacto directo por WhatsApp.', 'url' => '#'],
            ['thumb' => 5, 'category' => 'landing',     'tag' => 'Landing Page',      'title' => 'Black Gold Barbershop', 'text' => 'Landing page para barbería con reservas simplificadas por WhatsApp.', 'url' => '#'],
            ['thumb' => 6, 'category' => 'empresarial', 'tag' => 'Sitio Empresarial', 'title' => 'Pedraza Manufacturas',  'text' => 'Sitio institucional para fabricante de productos de identificación plástica.', 'url' => '#'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function plans(): array
    {
        return [
            [
                'name'     => 'Plan Básico',
                'desc'     => 'Para emprendedores que necesitan comenzar su presencia digital.',
                'price'    => '$XXX.XXX',
                'featured' => false,
                'features' => [
                    '1 página con secciones esenciales',
                    'Diseño responsive',
                    'Botón de contacto por WhatsApp',
                    'Formulario de contacto',
                    'Optimización básica de velocidad',
                ],
            ],
            [
                'name'     => 'Plan Profesional',
                'desc'     => 'Para empresas que necesitan una página más completa.',
                'price'    => '$XXX.XXX',
                'featured' => true,
                'badge'    => 'Más elegido',
                'features' => [
                    'Hasta 5 secciones/páginas',
                    'Diseño personalizado',
                    'Integración con WhatsApp',
                    'SEO básico incluido',
                    'Formularios avanzados',
                    'Soporte por 30 días',
                ],
            ],
            [
                'name'     => 'Plan Premium',
                'desc'     => 'Para empresas que buscan una presencia digital avanzada.',
                'price'    => '$XXX.XXX',
                'featured' => false,
                'features' => [
                    'Páginas y secciones ilimitadas*',
                    'Catálogo o portafolio dinámico',
                    'Integración avanzada con WhatsApp',
                    'SEO y rendimiento optimizado',
                    'Mantenimiento incluido',
                    'Soporte prioritario',
                ],
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public static function testimonials(): array
    {
        return [
            ['rating' => 5, 'quote' => 'ValiNix entendió exactamente lo que necesitábamos. Nuestra página quedó mucho más profesional que la anterior y ya recibimos consultas por WhatsApp todos los días.', 'author' => 'Camila Restrepo', 'company' => 'Nova Studio'],
            ['rating' => 5, 'quote' => 'El proceso fue muy claro desde el inicio. Nos explicaron cada etapa y entregaron el sitio antes de lo esperado.', 'author' => 'Julián Torres', 'company' => 'Andes Ingeniería'],
            ['rating' => 4, 'quote' => 'Nuestra tienda en línea se ve increíble y carga muy rápido. El soporte después de la entrega también fue excelente.', 'author' => 'Laura Gómez', 'company' => 'Verde Botánica'],
        ];
    }

    /** @return array<int, array<string, string>> */
    public static function faqs(): array
    {
        return [
            ['q' => '¿Cuánto tarda en desarrollarse una página web?', 'a' => 'Depende del plan y la complejidad, pero en promedio entre 1 y 3 semanas desde que confirmamos el contenido definitivo.'],
            ['q' => '¿La página funciona en celulares?', 'a' => 'Sí. Todos nuestros sitios se construyen con diseño responsive y se prueban en móvil, tablet y escritorio.'],
            ['q' => '¿Puedo solicitar cambios después de terminar el proyecto?', 'a' => 'Sí, cada plan incluye un periodo de ajustes y ofrecemos planes de mantenimiento para cambios posteriores.'],
            ['q' => '¿Incluyen dominio y hosting?', 'a' => 'Podemos asesorarte en la compra y configuración de dominio y hosting; su costo no está incluido en el desarrollo.'],
            ['q' => '¿Puedo conectar WhatsApp?', 'a' => 'Sí, la integración con WhatsApp está disponible desde el Plan Básico mediante un botón directo de contacto.'],
            ['q' => '¿La página queda optimizada para Google?', 'a' => 'Sí, aplicamos buenas prácticas de SEO básico: metadatos, estructura semántica y velocidad de carga.'],
            ['q' => '¿Ofrecen mantenimiento?', 'a' => 'Sí, contamos con planes de mantenimiento para actualizaciones, cambios de contenido y soporte continuo.'],
        ];
    }

    /** Opciones del campo "Tipo de proyecto": valor => etiqueta. @return array<string, string> */
    public static function projectTypes(): array
    {
        return [
            'landing'     => 'Landing page',
            'empresarial' => 'Sitio empresarial',
            'catalogo'    => 'Catálogo digital',
            'ecommerce'   => 'E-commerce',
            'otro'        => 'Otro',
        ];
    }

    /** Opciones del campo "Presupuesto aproximado": valor => etiqueta. @return array<string, string> */
    public static function budgetRanges(): array
    {
        return [
            ''            => 'Selecciona un rango',
            'basico'      => 'Rango plan básico',
            'profesional' => 'Rango plan profesional',
            'premium'     => 'Rango plan premium',
            'no-seguro'   => 'Aún no lo sé',
        ];
    }
}
