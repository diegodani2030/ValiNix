<?php
header('Content-Type: text/html; charset=UTF-8');
session_start(); 
require_once __DIR__ . '/includes/header.php';

$formStatus  = $_SESSION['form_status']  ?? null;
$formMessage = $_SESSION['form_message'] ?? null;
unset($_SESSION['form_status'], $_SESSION['form_message']);
?>

<main id="main">

    
    <section class="hero" id="inicio">
        <div class="hero__glow" aria-hidden="true"></div>
        <div class="container hero__inner">
            <div class="hero__content">
                <span class="eyebrow">Diseño &amp; desarrollo web</span>
                <h1 class="hero__title">Transformamos ideas en páginas web que <span class="text-accent">generan resultados.</span></h1>
                <p class="hero__text">Creamos sitios web modernos, rápidos y adaptables a cualquier dispositivo, pensados desde el primer clic para convertir visitantes en clientes.</p>
                <div class="hero__actions">
                    <a href="#contacto" class="btn btn--accent">Quiero mi página web</a>
                    <a href="#proyectos" class="btn btn--ghost">Ver proyectos</a>
                </div>

                <div class="hero__stats">
                    <div class="hero__stat">
                        <span class="hero__stat-number" data-counter data-target="50">0</span><span class="text-accent">+</span>
                        <span class="hero__stat-label">Proyectos</span>
                    </div>
                    <div class="hero__stat">
                        <span class="hero__stat-number" data-counter data-target="30">0</span><span class="text-accent">+</span>
                        <span class="hero__stat-label">Clientes</span>
                    </div>
                    <div class="hero__stat">
                        <span class="hero__stat-number" data-counter data-target="99">0</span><span class="text-accent">%</span>
                        <span class="hero__stat-label">Satisfacción</span>
                    </div>
                    <div class="hero__stat">
                        <span class="hero__stat-number hero__stat-number--text">1:1</span>
                        <span class="hero__stat-label">Soporte personalizado</span>
                    </div>
                </div>
            </div>

            <div class="hero__visual" aria-hidden="true">
                <div class="code-window">
                    <div class="code-window__bar">
                        <span class="code-dot code-dot--red"></span>
                        <span class="code-dot code-dot--yellow"></span>
                        <span class="code-dot code-dot--green"></span>
                        <span class="code-window__file">index.php</span>
                    </div>
                    <pre class="code-window__body"><code id="typedCode"></code><span class="code-caret"></span></pre>
                </div>

                <div class="floating-card floating-card--1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3v18M3 12h18" stroke="var(--accent)" stroke-width="2" stroke-linecap="round"/></svg>
                    <span>Sitio publicado</span>
                </div>
                <div class="floating-card floating-card--2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 12l5 5L20 6" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Optimizado para SEO</span>
                </div>
                <div class="floating-card floating-card--3">
                    <span class="floating-card__pulse"></span>
                    <span>+128% tráfico</span>
                </div>
            </div>
        </div>
    </section>

    
    <section class="problem" id="problema">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">El punto de partida</span>
                <h2 class="section-title">Tu negocio merece más que una página web.</h2>
            </div>

            <div class="problem__grid">
                <div class="problem__item reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 4l16 16M20 4L4 20" stroke="#F2607A" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p>Tener una página desactualizada que no refleja lo que tu negocio es hoy.</p>
                </div>
                <div class="problem__item reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 4l16 16M20 4L4 20" stroke="#F2607A" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p>No verse bien —o directamente no funcionar— en celulares.</p>
                </div>
                <div class="problem__item reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 4l16 16M20 4L4 20" stroke="#F2607A" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p>Perder clientes porque no encuentran la información que buscan.</p>
                </div>
                <div class="problem__item reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 4l16 16M20 4L4 20" stroke="#F2607A" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p>No tener una presencia profesional que genere confianza en internet.</p>
                </div>
                <div class="problem__item reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 4l16 16M20 4L4 20" stroke="#F2607A" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p>Depender únicamente de redes sociales para vender.</p>
                </div>
                <div class="problem__item reveal">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 4l16 16M20 4L4 20" stroke="#F2607A" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <p>No tener un canal de contacto rápido y eficiente, como WhatsApp.</p>
                </div>
            </div>

            <p class="problem__solution reveal">En ValiNix resolvemos esto: construimos tu presencia digital desde cero, con estrategia, diseño y código pensados para que tu negocio crezca.</p>
        </div>
    </section>

    
    <section class="services" id="servicios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Lo que hacemos</span>
                <h2 class="section-title">Servicios diseñados para cada etapa de tu negocio.</h2>
            </div>

            <div class="services__grid">
                <div class="service-card reveal">
                    <span class="service-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="3" y="4" width="18" height="14" rx="2" stroke="var(--accent)" stroke-width="1.6"/><path d="M3 9h18M8 21h8" stroke="var(--accent)" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </span>
                    <h3>Diseño web</h3>
                    <p>Sitios modernos y profesionales adaptados a la identidad de cada empresa.</p>
                </div>
                <div class="service-card reveal">
                    <span class="service-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M4 20V10l8-6 8 6v10" stroke="var(--accent)" stroke-width="1.6" stroke-linejoin="round"/><path d="M10 20v-6h4v6" stroke="var(--accent)" stroke-width="1.6"/></svg>
                    </span>
                    <h3>Landing pages</h3>
                    <p>Páginas enfocadas en un solo objetivo: convertir visitantes en clientes.</p>
                </div>
                <div class="service-card reveal">
                    <span class="service-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="4" y="3" width="16" height="18" rx="2" stroke="var(--accent)" stroke-width="1.6"/><path d="M8 8h8M8 12h8M8 16h5" stroke="var(--accent)" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </span>
                    <h3>Sitios empresariales</h3>
                    <p>Presencia digital completa para empresas y organizaciones.</p>
                </div>
                <div class="service-card reveal">
                    <span class="service-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="8" height="8" rx="1.4" stroke="var(--accent)" stroke-width="1.6"/><rect x="13" y="5" width="8" height="8" rx="1.4" stroke="var(--accent)" stroke-width="1.6"/><rect x="3" y="15" width="8" height="6" rx="1.4" stroke="var(--accent)" stroke-width="1.6"/><rect x="13" y="15" width="8" height="6" rx="1.4" stroke="var(--accent)" stroke-width="1.6"/></svg>
                    </span>
                    <h3>Catálogos digitales</h3>
                    <p>Presenta tus productos y servicios de forma clara y profesional.</p>
                </div>
                <div class="service-card reveal">
                    <span class="service-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z" stroke="var(--accent)" stroke-width="1.6"/></svg>
                    </span>
                    <h3>Integración con WhatsApp</h3>
                    <p>Facilitamos que tus visitantes te contacten directamente desde la web.</p>
                </div>
                <div class="service-card reveal">
                    <span class="service-card__icon">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M4 19V9M11 19V5M18 19v-7" stroke="var(--accent)" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </span>
                    <h3>SEO y rendimiento</h3>
                    <p>Optimizamos velocidad, estructura y posicionamiento en buscadores.</p>
                </div>
            </div>
        </div>
    </section>

    
    <section class="benefits" id="beneficios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Por qué ValiNix</span>
                <h2 class="section-title">Cada sitio incluye lo que realmente importa.</h2>
            </div>

            <div class="benefits__grid">
                <div class="benefit reveal"><span class="benefit__dot"></span>Diseño responsive</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Carga rápida</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Optimización SEO</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Código limpio</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Seguridad</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Integración con WhatsApp</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Formularios de contacto</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Diseño personalizado</div>
                <div class="benefit reveal"><span class="benefit__dot"></span>Soporte técnico</div>
            </div>
        </div>
    </section>

    
    <section class="process" id="proceso">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Cómo trabajamos</span>
                <h2 class="section-title">Un proceso claro, de principio a fin.</h2>
            </div>

            <ol class="timeline">
                <li class="timeline__step reveal">
                    <span class="timeline__num">01</span>
                    <h3>Cuéntanos tu idea</h3>
                    <p>Nos compartes qué necesitas, cómo es tu negocio y qué quieres lograr con tu página.</p>
                </li>
                <li class="timeline__step reveal">
                    <span class="timeline__num">02</span>
                    <h3>Analizamos tus necesidades</h3>
                    <p>Definimos alcance, estructura de contenido y el plan que mejor se ajusta a tu proyecto.</p>
                </li>
                <li class="timeline__step reveal">
                    <span class="timeline__num">03</span>
                    <h3>Diseñamos tu página</h3>
                    <p>Creamos una propuesta visual alineada a tu marca antes de escribir una sola línea de código.</p>
                </li>
                <li class="timeline__step reveal">
                    <span class="timeline__num">04</span>
                    <h3>Desarrollamos y optimizamos</h3>
                    <p>Construimos el sitio, lo probamos en distintos dispositivos y lo optimizamos para velocidad y SEO.</p>
                </li>
                <li class="timeline__step reveal">
                    <span class="timeline__num">05</span>
                    <h3>Publicamos tu sitio</h3>
                    <p>Ponemos tu página en línea y te acompañamos con soporte una vez esté publicada.</p>
                </li>
            </ol>
        </div>
    </section>

    
    <section class="portfolio" id="proyectos">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Proyectos</span>
                <h2 class="section-title">Algunos ejemplos de nuestro trabajo.</h2>
            </div>

            <div class="portfolio__filters" role="tablist" aria-label="Filtrar proyectos">
                <button class="filter-btn is-active" data-filter="all">Todos</button>
                <button class="filter-btn" data-filter="landing">Landing Pages</button>
                <button class="filter-btn" data-filter="empresarial">Empresariales</button>
                <button class="filter-btn" data-filter="ecommerce">E-commerce</button>
                <button class="filter-btn" data-filter="catalogo">Catálogos</button>
            </div>

            <div class="portfolio__grid" id="portfolioGrid">
                <article class="project-card" data-category="landing">
                    <div class="project-card__thumb project-card__thumb--1"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag">Landing Page</span>
                        <h3>Nova Studio</h3>
                        <p>Landing page para estudio de diseño de interiores enfocada en agendar visitas.</p>
                        <a href="#" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
                <article class="project-card" data-category="empresarial">
                    <div class="project-card__thumb project-card__thumb--2"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag">Sitio Empresarial</span>
                        <h3>Andes Ingeniería</h3>
                        <p>Sitio corporativo con portafolio de proyectos y sección institucional completa.</p>
                        <a href="#" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
                <article class="project-card" data-category="ecommerce">
                    <div class="project-card__thumb project-card__thumb--3"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag">E-commerce</span>
                        <h3>Verde Botánica</h3>
                        <p>Tienda en línea de plantas y accesorios con carrito de compras integrado.</p>
                        <a href="#" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
                <article class="project-card" data-category="catalogo">
                    <div class="project-card__thumb project-card__thumb--4"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag">Catálogo Digital</span>
                        <h3>Muebles del Valle</h3>
                        <p>Catálogo digital de mobiliario con fichas técnicas y contacto directo por WhatsApp.</p>
                        <a href="#" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
                <article class="project-card" data-category="landing">
                    <div class="project-card__thumb project-card__thumb--5"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag">Landing Page</span>
                        <h3>Black Gold Barbershop</h3>
                        <p>Landing page para barbería con reservas simplificadas por WhatsApp.</p>
                        <a href="#" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
                <article class="project-card" data-category="empresarial">
                    <div class="project-card__thumb project-card__thumb--6"></div>
                    <div class="project-card__body">
                        <span class="project-card__tag">Sitio Empresarial</span>
                        <h3>Pedraza Manufacturas</h3>
                        <p>Sitio institucional para fabricante de productos de identificación plástica.</p>
                        <a href="#" class="project-card__link">Ver proyecto →</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    
    <section class="pricing" id="precios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Planes</span>
                <h2 class="section-title">Un plan para cada etapa de tu negocio.</h2>
            </div>

            <div class="pricing__grid">
                <div class="price-card reveal">
                    <h3 class="price-card__name">Plan Básico</h3>
                    <p class="price-card__desc">Para emprendedores que necesitan comenzar su presencia digital.</p>
                    <p class="price-card__price">$XXX.XXX</p>
                    <ul class="price-card__features">
                        <li>1 página con secciones esenciales</li>
                        <li>Diseño responsive</li>
                        <li>Botón de contacto por WhatsApp</li>
                        <li>Formulario de contacto</li>
                        <li>Optimización básica de velocidad</li>
                    </ul>
                    <a href="#contacto" class="btn btn--ghost btn--block">Solicitar cotización</a>
                </div>

                <div class="price-card price-card--featured reveal">
                    <span class="price-card__badge">Más elegido</span>
                    <h3 class="price-card__name">Plan Profesional</h3>
                    <p class="price-card__desc">Para empresas que necesitan una página más completa.</p>
                    <p class="price-card__price">$XXX.XXX</p>
                    <ul class="price-card__features">
                        <li>Hasta 5 secciones/páginas</li>
                        <li>Diseño personalizado</li>
                        <li>Integración con WhatsApp</li>
                        <li>SEO básico incluido</li>
                        <li>Formularios avanzados</li>
                        <li>Soporte por 30 días</li>
                    </ul>
                    <a href="#contacto" class="btn btn--accent btn--block">Solicitar cotización</a>
                </div>

                <div class="price-card reveal">
                    <h3 class="price-card__name">Plan Premium</h3>
                    <p class="price-card__desc">Para empresas que buscan una presencia digital avanzada.</p>
                    <p class="price-card__price">$XXX.XXX</p>
                    <ul class="price-card__features">
                        <li>Páginas y secciones ilimitadas*</li>
                        <li>Catálogo o portafolio dinámico</li>
                        <li>Integración avanzada con WhatsApp</li>
                        <li>SEO y rendimiento optimizado</li>
                        <li>Mantenimiento incluido</li>
                        <li>Soporte prioritario</li>
                    </ul>
                    <a href="#contacto" class="btn btn--ghost btn--block">Solicitar cotización</a>
                </div>
            </div>
        </div>
    </section>

    
    <section class="testimonials" id="testimonios">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Lo que dicen nuestros clientes</span>
                <h2 class="section-title">Contenido demostrativo de clientes ficticios.</h2>
            </div>

            <div class="testimonial-slider" id="testimonialSlider">
                <div class="testimonial-track" id="testimonialTrack">
                    <div class="testimonial-slide is-active">
                        <div class="testimonial-stars" aria-label="5 de 5 estrellas">★★★★★</div>
                        <p class="testimonial-quote">ValiNix entendió exactamente lo que necesitábamos. Nuestra página quedó mucho más profesional que la anterior y ya recibimos consultas por WhatsApp todos los días.</p>
                        <p class="testimonial-author">Camila Restrepo <span>— Nova Studio</span></p>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-stars" aria-label="5 de 5 estrellas">★★★★★</div>
                        <p class="testimonial-quote">El proceso fue muy claro desde el inicio. Nos explicaron cada etapa y entregaron el sitio antes de lo esperado.</p>
                        <p class="testimonial-author">Julián Torres <span>— Andes Ingeniería</span></p>
                    </div>
                    <div class="testimonial-slide">
                        <div class="testimonial-stars" aria-label="4 de 5 estrellas">★★★★☆</div>
                        <p class="testimonial-quote">Nuestra tienda en línea se ve increíble y carga muy rápido. El soporte después de la entrega también fue excelente.</p>
                        <p class="testimonial-author">Laura Gómez <span>— Verde Botánica</span></p>
                    </div>
                </div>

                <div class="testimonial-controls">
                    <button class="testimonial-btn" id="testimonialPrev" aria-label="Testimonio anterior">‹</button>
                    <div class="testimonial-dots" id="testimonialDots"></div>
                    <button class="testimonial-btn" id="testimonialNext" aria-label="Testimonio siguiente">›</button>
                </div>
            </div>
        </div>
    </section>

    
    <section class="faq" id="faq">
        <div class="container">
            <div class="section-head">
                <span class="eyebrow">Dudas frecuentes</span>
                <h2 class="section-title">Preguntas frecuentes</h2>
            </div>

            <div class="faq__list">
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿Cuánto tarda en desarrollarse una página web?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Depende del plan y la complejidad, pero en promedio entre 1 y 3 semanas desde que confirmamos el contenido definitivo.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿La página funciona en celulares?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Sí. Todos nuestros sitios se construyen con diseño responsive y se prueban en móvil, tablet y escritorio.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿Puedo solicitar cambios después de terminar el proyecto?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Sí, cada plan incluye un periodo de ajustes y ofrecemos planes de mantenimiento para cambios posteriores.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿Incluyen dominio y hosting?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Podemos asesorarte en la compra y configuración de dominio y hosting; su costo no está incluido en el desarrollo.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿Puedo conectar WhatsApp?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Sí, la integración con WhatsApp está disponible desde el Plan Básico mediante un botón directo de contacto.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿La página queda optimizada para Google?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Sí, aplicamos buenas prácticas de SEO básico: metadatos, estructura semántica y velocidad de carga.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-item__question" aria-expanded="false">
                        <span>¿Ofrecen mantenimiento?</span>
                        <span class="faq-item__icon">+</span>
                    </button>
                    <div class="faq-item__answer">
                        <p>Sí, contamos con planes de mantenimiento para actualizaciones, cambios de contenido y soporte continuo.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <section class="cta-final">
        <div class="container cta-final__inner reveal">
            <h2>¿Listo para llevar tu negocio a internet?</h2>
            <p>Cuéntanos qué necesitas y creemos juntos una presencia digital profesional para tu negocio.</p>
            <div class="cta-final__actions">
                <a href="#contacto" class="btn btn--accent">Solicitar cotización</a>
                <a href="https://wa.me/573000000000" target="_blank" rel="noopener" class="btn btn--ghost">Hablar por WhatsApp</a>
            </div>
        </div>
    </section>

    
    <section class="contact" id="contacto">
        <div class="container contact__inner">
            <div class="contact__info reveal">
                <span class="eyebrow">Hablemos</span>
                <h2 class="section-title">Cuéntanos sobre tu proyecto</h2>
                <p class="contact__lead">Completa el formulario y te responderemos a la brevedad, o escríbenos directamente por WhatsApp.</p>

                <ul class="contact__details">
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z" stroke="var(--accent)" stroke-width="1.6"/></svg>
                        <a href="https://wa.me/573000000000">+57 300 000 0000</a>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="var(--accent)" stroke-width="1.6"/><path d="M3 7l9 6 9-6" stroke="var(--accent)" stroke-width="1.6"/></svg>
                        <a href="mailto:hola@valinix.software">hola@valinix.software</a>
                    </li>
                    <li>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="var(--accent)" stroke-width="1.6"/><path d="M12 7v5l3 2" stroke="var(--accent)" stroke-width="1.6" stroke-linecap="round"/></svg>
                        <span>Lunes a viernes · 8:00 a.m. – 6:00 p.m.</span>
                    </li>
                </ul>
            </div>

            <form class="contact__form reveal" id="contactForm" action="includes/process_form.php" method="POST" novalidate>
                <?php if ($formStatus === 'success'): ?>
                    <div class="form-alert form-alert--success"><?php echo htmlspecialchars($formMessage); ?></div>
                <?php elseif ($formStatus === 'error'): ?>
                    <div class="form-alert form-alert--error"><?php echo htmlspecialchars($formMessage); ?></div>
                <?php endif; ?>

                <!-- Honeypot field (hidden from real users) -->
                <div class="hp-field" aria-hidden="true">
                    <label for="website">No llenar este campo</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" required minlength="2">
                        <span class="form-error" data-error-for="nombre"></span>
                    </div>
                    <div class="form-group">
                        <label for="empresa">Empresa</label>
                        <input type="text" id="empresa" name="empresa">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="correo">Correo electrónico *</label>
                        <input type="email" id="correo" name="correo" required>
                        <span class="form-error" data-error-for="correo"></span>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" id="telefono" name="telefono" required>
                        <span class="form-error" data-error-for="telefono"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tipo_proyecto">Tipo de proyecto</label>
                        <select id="tipo_proyecto" name="tipo_proyecto">
                            <option value="landing">Landing page</option>
                            <option value="empresarial">Sitio empresarial</option>
                            <option value="catalogo">Catálogo digital</option>
                            <option value="ecommerce">E-commerce</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="presupuesto">Presupuesto aproximado</label>
                        <select id="presupuesto" name="presupuesto">
                            <option value="">Selecciona un rango</option>
                            <option value="basico">Rango plan básico</option>
                            <option value="profesional">Rango plan profesional</option>
                            <option value="premium">Rango plan premium</option>
                            <option value="no-seguro">Aún no lo sé</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje *</label>
                    <textarea id="mensaje" name="mensaje" rows="4" required minlength="10"></textarea>
                    <span class="form-error" data-error-for="mensaje"></span>
                </div>

                <button type="submit" class="btn btn--accent btn--block">Enviar solicitud</button>
            </form>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
