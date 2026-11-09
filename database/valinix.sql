-- =====================================================================
--  ValiNix — Base de datos (MySQL / MariaDB)
--
--  Cómo importarla:
--   * InfinityFree: Panel → MySQL Databases → phpMyAdmin de tu base →
--     pestaña "Importar" → elegir este archivo → Continuar.
--   * XAMPP (local): crea primero la base "valinix_db" en phpMyAdmin
--     (cotejamiento utf8mb4_unicode_ci), selecciónala e importa este archivo.
--
--  OJO: al importarlo se BORRAN las tablas existentes y se vuelven a crear
--  con los datos iniciales. Úsalo para instalar, no para actualizar.
--
--  Usuario inicial del panel:  admin  /  valinix2026
--  Es solo para el primer ingreso: el sistema obliga a cambiarla antes de
--  dejar usar el panel. Entra y cámbiala apenas importes la base.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS intentos_login;
DROP TABLE IF EXISTS mensajes;
DROP TABLE IF EXISTS testimonios;
DROP TABLE IF EXISTS plan_caracteristicas;
DROP TABLE IF EXISTS planes;
DROP TABLE IF EXISTS faqs;
DROP TABLE IF EXISTS proyectos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS servicios;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- roles: tipos de usuario que pueden iniciar sesión.
-- (El "visitante" no necesita cuenta, por eso no es un rol.)
-- ---------------------------------------------------------------------
CREATE TABLE roles (
    id          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(40)  NOT NULL,
    descripcion VARCHAR(200) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- usuarios: cuentas del panel. Cada usuario tiene un rol (N:1).
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    rol_id         TINYINT UNSIGNED NOT NULL,
    nombre         VARCHAR(100)     NOT NULL,
    nombre_usuario VARCHAR(40)      NOT NULL,
    correo         VARCHAR(150)     NOT NULL,
    contrasena     VARCHAR(255)     NOT NULL COMMENT 'Hash de password_hash(), nunca texto plano',
    activo         TINYINT(1)       NOT NULL DEFAULT 1,
    debe_cambiar_contrasena TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = debe elegir una contraseña nueva al entrar',
    creado_en      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME         NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_nombre_usuario (nombre_usuario),
    UNIQUE KEY uq_usuarios_correo (correo),
    KEY idx_usuarios_rol (rol_id),
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- servicios: lo que ofrece ValiNix (página /servicios).
-- ---------------------------------------------------------------------
CREATE TABLE servicios (
    id          INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    titulo      VARCHAR(80)       NOT NULL,
    descripcion VARCHAR(255)      NOT NULL,
    icono       VARCHAR(40)       NOT NULL DEFAULT 'service-web',
    orden       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    activo      TINYINT(1)        NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_servicios_titulo (titulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- categorias: tipos de proyecto del portafolio (filtros de /proyectos).
-- ---------------------------------------------------------------------
CREATE TABLE categorias (
    id     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug   VARCHAR(30)      NOT NULL COMMENT 'Identificador para la URL y los filtros',
    nombre VARCHAR(60)      NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- proyectos: portafolio. Cada proyecto pertenece a una categoría (N:1)
-- y guarda qué usuario lo registró (N:1, opcional).
-- ---------------------------------------------------------------------
CREATE TABLE proyectos (
    id             INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    categoria_id   TINYINT UNSIGNED NOT NULL,
    usuario_id     INT UNSIGNED     NULL DEFAULT NULL,
    titulo         VARCHAR(100)     NOT NULL,
    descripcion    VARCHAR(255)     NOT NULL,
    enlace         VARCHAR(255)     NOT NULL DEFAULT '#',
    color          TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Estilo de la miniatura (1 a 6)',
    creado_en      DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME         NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_proyectos_categoria (categoria_id),
    KEY idx_proyectos_usuario (usuario_id),
    CONSTRAINT fk_proyectos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_proyectos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_proyectos_color CHECK (color BETWEEN 1 AND 6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- planes y plan_caracteristicas: página /precios (1:N).
-- ---------------------------------------------------------------------
CREATE TABLE planes (
    id          TINYINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    clave       VARCHAR(20)       NOT NULL COMMENT 'Se usa en /contacto?plan=clave',
    nombre      VARCHAR(60)       NOT NULL,
    descripcion VARCHAR(200)      NOT NULL,
    precio      VARCHAR(30)       NOT NULL,
    destacado   TINYINT(1)        NOT NULL DEFAULT 0,
    etiqueta    VARCHAR(30)       NULL DEFAULT NULL COMMENT 'Ej. "Más elegido"',
    orden       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_planes_clave (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE plan_caracteristicas (
    id      INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    plan_id TINYINT UNSIGNED  NOT NULL,
    texto   VARCHAR(120)      NOT NULL,
    orden   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_caracteristicas_plan (plan_id),
    CONSTRAINT fk_caracteristicas_plan FOREIGN KEY (plan_id) REFERENCES planes (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- testimonios: opiniones de clientes (inicio).
-- ---------------------------------------------------------------------
CREATE TABLE testimonios (
    id           INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    autor        VARCHAR(100)      NOT NULL,
    empresa      VARCHAR(100)      NOT NULL,
    calificacion TINYINT UNSIGNED  NOT NULL DEFAULT 5,
    comentario   VARCHAR(500)      NOT NULL,
    orden        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    CONSTRAINT chk_testimonios_calificacion CHECK (calificacion BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- faqs: preguntas frecuentes.
-- ---------------------------------------------------------------------
CREATE TABLE faqs (
    id        INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    pregunta  VARCHAR(200)      NOT NULL,
    respuesta VARCHAR(500)      NOT NULL,
    orden     SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- mensajes: lo que llega desde el formulario de /contacto.
-- Se relaciona con el servicio y el plan que eligió el cliente (N:1, opcionales).
-- ---------------------------------------------------------------------
CREATE TABLE mensajes (
    id          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    servicio_id INT UNSIGNED     NULL DEFAULT NULL,
    plan_id     TINYINT UNSIGNED NULL DEFAULT NULL,
    nombre      VARCHAR(100)     NOT NULL,
    empresa     VARCHAR(120)     NULL DEFAULT NULL,
    correo      VARCHAR(150)     NOT NULL,
    telefono    VARCHAR(25)      NOT NULL,
    asunto      VARCHAR(150)     NOT NULL,
    mensaje     TEXT             NOT NULL,
    leido       TINYINT(1)       NOT NULL DEFAULT 0,
    creado_en   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_mensajes_servicio (servicio_id),
    KEY idx_mensajes_plan (plan_id),
    KEY idx_mensajes_leido (leido, creado_en),
    CONSTRAINT fk_mensajes_servicio FOREIGN KEY (servicio_id) REFERENCES servicios (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_mensajes_plan FOREIGN KEY (plan_id) REFERENCES planes (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- intentos_login: intentos fallidos de inicio de sesión (protección contra
-- ataques de fuerza bruta). Se guardan en el servidor, no en la sesión.
-- ---------------------------------------------------------------------
CREATE TABLE intentos_login (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    clave     VARCHAR(150) NOT NULL COMMENT 'Usuario o correo que se intentó (en minúsculas)',
    ip        VARCHAR(45)  NOT NULL,
    creado_en DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_intentos_clave (clave, creado_en),
    KEY idx_intentos_ip (ip, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  Datos iniciales
-- =====================================================================

INSERT INTO roles (id, nombre, descripcion) VALUES
(1, 'Administrador',      'Gestiona usuarios, servicios, proyectos, testimonios y mensajes. Acceso total al panel.'),
(2, 'Usuario registrado', 'Consulta la información del panel y administra su propia cuenta. No puede modificar datos.');

-- Contraseña: valinix2026 (hash generado con password_hash de PHP).
-- debe_cambiar_contrasena = 1: al primer ingreso el panel exige cambiarla.
INSERT INTO usuarios (id, rol_id, nombre, nombre_usuario, correo, contrasena, debe_cambiar_contrasena) VALUES
(1, 1, 'Administrador ValiNix', 'admin', 'admin@valinix.software', '$2y$10$0HCaGiiaGBDkHTjfzfAyaeaDCUzGrVsCJbNOHMinVwLBHQmV6lZkG', 1);

INSERT INTO servicios (id, titulo, descripcion, icono, orden) VALUES
(1, 'Desarrollo web',           'Creación de páginas y aplicaciones web modernas, adaptadas a la identidad de cada empresa.', 'service-web',      1),
(2, 'Sistemas de información',  'Soluciones para gestionar datos: inventarios, ventas, clientes y reportes en un solo lugar.', 'service-catalog',  2),
(3, 'Seguridad informática',    'Protección de tu información: accesos con contraseña, copias de seguridad y buenas prácticas.', 'service-shield', 3),
(4, 'Soporte y mantenimiento',  'Ayuda técnica continua: actualizaciones, cambios de contenido y resolución de fallas.', 'service-support',  4),
(5, 'Landing pages',            'Páginas enfocadas en un solo objetivo: convertir visitantes en clientes.', 'service-landing',  5),
(6, 'Integración con WhatsApp', 'Facilitamos que tus visitantes te contacten directamente desde la web.', 'whatsapp-outline', 6);

INSERT INTO categorias (id, slug, nombre) VALUES
(1, 'gastronomia', 'Gastronomía'),
(2, 'social',      'Emprendimiento social'),
(3, 'landing',     'Landing Pages'),
(4, 'empresarial', 'Empresariales'),
(5, 'ecommerce',   'E-commerce'),
(6, 'catalogo',    'Catálogos');

INSERT INTO proyectos (id, categoria_id, usuario_id, titulo, descripcion, enlace, color) VALUES
(1, 1, 1, 'Raíces del Fogón',     'Sistema de información para la gestión de ventas e inventarios de un establecimiento gastronómico.', 'https://raicesfogon.free.je', 4),
(2, 2, 1, 'Rincón Tinto Animal',  'Sitio para un café con enfoque en la adopción responsable de animales.', '#', 5),
(3, 3, 1, 'Nova Studio',          'Landing page para estudio de diseño de interiores enfocada en agendar visitas.', '#', 1),
(4, 4, 1, 'Andes Ingeniería',     'Sitio corporativo con portafolio de proyectos y sección institucional completa.', '#', 2),
(5, 5, 1, 'Verde Botánica',       'Tienda en línea de plantas y accesorios con carrito de compras integrado.', '#', 3),
(6, 6, 1, 'Muebles del Valle',    'Catálogo digital de mobiliario con fichas técnicas y contacto directo por WhatsApp.', '#', 6);

INSERT INTO planes (id, clave, nombre, descripcion, precio, destacado, etiqueta, orden) VALUES
(1, 'basico',      'Plan Básico',      'Para emprendedores que necesitan comenzar su presencia digital.', '$XXX.XXX', 0, NULL,          1),
(2, 'profesional', 'Plan Profesional', 'Para empresas que necesitan una página más completa.',            '$XXX.XXX', 1, 'Más elegido', 2),
(3, 'premium',     'Plan Premium',     'Para empresas que buscan una presencia digital avanzada.',        '$XXX.XXX', 0, NULL,          3);

INSERT INTO plan_caracteristicas (plan_id, texto, orden) VALUES
(1, '1 página con secciones esenciales', 1),
(1, 'Diseño responsive', 2),
(1, 'Botón de contacto por WhatsApp', 3),
(1, 'Formulario de contacto', 4),
(1, 'Optimización básica de velocidad', 5),
(2, 'Hasta 5 secciones/páginas', 1),
(2, 'Diseño personalizado', 2),
(2, 'Integración con WhatsApp', 3),
(2, 'SEO básico incluido', 4),
(2, 'Formularios avanzados', 5),
(2, 'Soporte por 30 días', 6),
(3, 'Páginas y secciones ilimitadas*', 1),
(3, 'Catálogo o portafolio dinámico', 2),
(3, 'Integración avanzada con WhatsApp', 3),
(3, 'SEO y rendimiento optimizado', 4),
(3, 'Mantenimiento incluido', 5),
(3, 'Soporte prioritario', 6);

INSERT INTO testimonios (autor, empresa, calificacion, comentario, orden) VALUES
('Camila Restrepo', 'Nova Studio',      5, 'ValiNix entendió exactamente lo que necesitábamos. Nuestra página quedó mucho más profesional que la anterior y ya recibimos consultas por WhatsApp todos los días.', 1),
('Julián Torres',   'Andes Ingeniería', 5, 'El proceso fue muy claro desde el inicio. Nos explicaron cada etapa y entregaron el sitio antes de lo esperado.', 2),
('Laura Gómez',     'Verde Botánica',   4, 'Nuestra tienda en línea se ve increíble y carga muy rápido. El soporte después de la entrega también fue excelente.', 3);

INSERT INTO faqs (pregunta, respuesta, orden) VALUES
('¿Cuánto tarda en desarrollarse una página web?', 'Depende del plan y la complejidad, pero en promedio entre 1 y 3 semanas desde que confirmamos el contenido definitivo.', 1),
('¿La página funciona en celulares?', 'Sí. Todos nuestros sitios se construyen con diseño responsive y se prueban en móvil, tablet y escritorio.', 2),
('¿Puedo solicitar cambios después de terminar el proyecto?', 'Sí, cada plan incluye un periodo de ajustes y ofrecemos planes de mantenimiento para cambios posteriores.', 3),
('¿Incluyen dominio y hosting?', 'Podemos asesorarte en la compra y configuración de dominio y hosting; su costo no está incluido en el desarrollo.', 4),
('¿Puedo conectar WhatsApp?', 'Sí, la integración con WhatsApp está disponible desde el Plan Básico mediante un botón directo de contacto.', 5),
('¿La página queda optimizada para Google?', 'Sí, aplicamos buenas prácticas de SEO básico: metadatos, estructura semántica y velocidad de carga.', 6),
('¿Ofrecen mantenimiento?', 'Sí, contamos con planes de mantenimiento para actualizaciones, cambios de contenido y soporte continuo.', 7);
