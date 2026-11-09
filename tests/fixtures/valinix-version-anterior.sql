-- Copia de la base de la versión anterior (ValiNix-MVC) para probar la migración.
-- Generada a partir de database/valinix.sql y actualizar_datos.sql de esa versión, más datos de ejemplo.
SET NAMES utf8mb4;
-- =========================================================
--  ValiNix — Base de datos MVC
--  Importar este archivo desde phpMyAdmin: pestaña "Importar"
--  o pegarlo en la pestaña "SQL" de una base de datos nueva.
-- =========================================================



-- ---------------------------------------------------------
-- Tabla: usuarios (administradores del panel)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(60) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario: admin  |  Contraseña: valinix2026
INSERT INTO usuarios (nombre_usuario, contrasena) VALUES
('admin', '$2b$10$b5Z0FpNoXM1gSMW2/0XVd.FrB6ZWeksBpgZ/cOs2K.faGX8MB887C');

-- ---------------------------------------------------------
-- Tabla: mensajes (formulario de contacto)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    empresa VARCHAR(120) DEFAULT NULL,
    correo VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    tipo_proyecto VARCHAR(60) DEFAULT NULL,
    presupuesto VARCHAR(60) DEFAULT NULL,
    mensaje TEXT NOT NULL,
    leido TINYINT(1) NOT NULL DEFAULT 0,
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------
-- Tabla: servicios
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(120) NOT NULL,
    descripcion TEXT NOT NULL,
    icono VARCHAR(60) DEFAULT 'briefcase',
    orden INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO servicios (titulo, descripcion, icono, orden) VALUES
('Desarrollo web', 'Creación de páginas y aplicaciones web.', 'layout', 1),
('Sistemas de información', 'Soluciones para gestionar datos.', 'grid', 2),
('Seguridad informática', 'Protección de tu información.', 'shield', 3),
('Soporte y mantenimiento', 'Ayuda técnica continua.', 'chat', 4);

-- ---------------------------------------------------------
-- Tabla: proyectos (portafolio)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    categoria VARCHAR(40) NOT NULL,
    descripcion TEXT NOT NULL,
    imagen_color VARCHAR(10) DEFAULT '1',
    enlace VARCHAR(255) DEFAULT '#',
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO proyectos (titulo, categoria, descripcion, imagen_color, enlace) VALUES
('Raíces del Fogón', 'gastronomia', 'Proyecto gastronómico desarrollado por ValiNix.', '4', '#'),
('Rincón Tinto Animal', 'adopcion', 'Proyecto de café y adopción animal.', '5', '#');

-- ---------------------------------------------------------
-- Tabla: planes de precios
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS planes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio VARCHAR(40) NOT NULL,
    destacado TINYINT(1) DEFAULT 0,
    orden INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO planes (nombre, descripcion, precio, destacado, orden) VALUES
('Plan Básico', 'Para emprendedores que necesitan comenzar su presencia digital.', '$XXX.XXX', 0, 1),
('Plan Profesional', 'Para empresas que necesitan una página más completa.', '$XXX.XXX', 1, 2),
('Plan Premium', 'Para empresas que buscan una presencia digital avanzada.', '$XXX.XXX', 0, 3);

-- ---------------------------------------------------------
-- Tabla: características de cada plan (relación 1:N con planes)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS plan_caracteristicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_id INT NOT NULL,
    texto VARCHAR(150) NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (plan_id) REFERENCES planes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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

-- ---------------------------------------------------------
-- Tabla: testimonios
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS testimonios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(120) NOT NULL,
    empresa_cliente VARCHAR(120) DEFAULT NULL,
    calificacion TINYINT DEFAULT 5,
    comentario TEXT NOT NULL,
    orden INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- (Sin testimonios de ejemplo: agrégalos desde el panel cuando tengas reales.)

-- ---------------------------------------------------------
-- Tabla: preguntas frecuentes
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    respuesta TEXT NOT NULL,
    orden INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO faqs (pregunta, respuesta, orden) VALUES
('¿Cuánto tarda en desarrollarse una página web?', 'Depende del plan y la complejidad, pero en promedio entre 1 y 3 semanas desde que confirmamos el contenido definitivo.', 1),
('¿La página funciona en celulares?', 'Sí. Todos nuestros sitios se construyen con diseño responsive y se prueban en móvil, tablet y escritorio.', 2),
('¿Puedo solicitar cambios después de terminar el proyecto?', 'Sí, cada plan incluye un periodo de ajustes y ofrecemos planes de mantenimiento para cambios posteriores.', 3),
('¿Incluyen dominio y hosting?', 'Podemos asesorarte en la compra y configuración de dominio y hosting; su costo no está incluido en el desarrollo.', 4),
('¿Puedo conectar WhatsApp?', 'Sí, la integración con WhatsApp está disponible desde el Plan Básico mediante un botón directo de contacto.', 5),
('¿La página queda optimizada para Google?', 'Sí, aplicamos buenas prácticas de SEO básico: metadatos, estructura semántica y velocidad de carga.', 6),
('¿Ofrecen mantenimiento?', 'Sí, contamos con planes de mantenimiento para actualizaciones, cambios de contenido y soporte continuo.', 7);
-- Ejecutar en phpMyAdmin (pestaña SQL) sobre valinix_db si ya importaste la base anterior.
DELETE FROM servicios;
INSERT INTO servicios (titulo, descripcion, icono, orden) VALUES
('Desarrollo web', 'Creación de páginas y aplicaciones web.', 'layout', 1),
('Sistemas de información', 'Soluciones para gestionar datos.', 'grid', 2),
('Seguridad informática', 'Protección de tu información.', 'shield', 3),
('Soporte y mantenimiento', 'Ayuda técnica continua.', 'chat', 4);
DELETE FROM proyectos;
INSERT INTO proyectos (titulo, categoria, descripcion, imagen_color, enlace) VALUES
('Raíces del Fogón', 'gastronomia', 'Proyecto gastronómico desarrollado por ValiNix.', '4', '#'),
('Rincón Tinto Animal', 'adopcion', 'Proyecto de café y adopción animal.', '5', '#');
DELETE FROM testimonios;

-- Datos "reales" de ejemplo (como los que tendría la base en uso).
INSERT INTO mensajes (nombre, empresa, correo, telefono, tipo_proyecto, presupuesto, mensaje, leido, creado_en) VALUES
('Cliente Real', 'Panadería La 14', 'cliente@example.com', '3001234567', 'landing', 'profesional', 'Necesito una página para mi panadería.', 0, '2026-10-20 10:30:00');
INSERT INTO testimonios (nombre_cliente, empresa_cliente, calificacion, comentario, orden) VALUES
('María López', NULL, 5, 'Excelente atención y muy rápidos.', 1);
