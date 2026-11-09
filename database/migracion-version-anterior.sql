-- =====================================================================
--  ValiNix — Migración de la base de la versión anterior (ValiNix-MVC)
--  a la estructura nueva, SIN BORRAR LOS DATOS.
--
--  Úsala si ya tienes la base "valinix_db" con las 8 tablas de antes
--  (faqs, mensajes, planes, plan_caracteristicas, proyectos, servicios,
--  testimonios, usuarios). Si vas a empezar de cero, usa valinix.sql.
--
--  ANTES DE EJECUTARLA:
--   1. Haz una copia: phpMyAdmin → selecciona valinix_db → Exportar → Continuar.
--   2. Selecciona valinix_db → pestaña Importar → elige este archivo → Continuar.
--      (Ejecútala UNA sola vez.)
--
--  Qué hace:
--   * Crea las tablas nuevas: roles, categorias e intentos_login.
--   * Agrega a cada tabla las columnas nuevas y las llaves foráneas.
--   * Convierte los datos: la categoría de texto de cada proyecto pasa a
--     categoria_id, el "presupuesto" de cada mensaje pasa a plan_id, el
--     "tipo de proyecto" queda escrito en el asunto, etc.
--   * Los usuarios existentes quedan como Administrador y deben cambiar su
--     contraseña la próxima vez que entren.
-- =====================================================================

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 0. Mismo cotejamiento en todas las tablas (para poder unirlas por texto).
-- ---------------------------------------------------------------------
ALTER TABLE usuarios             CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE mensajes             CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE servicios            CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE proyectos            CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE planes               CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE plan_caracteristicas CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE testimonios          CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE faqs                 CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 1. roles (nueva)
-- ---------------------------------------------------------------------
CREATE TABLE roles (
    id          TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(40)  NOT NULL,
    descripcion VARCHAR(200) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (id, nombre, descripcion) VALUES
(1, 'Administrador',      'Gestiona usuarios, servicios, proyectos, testimonios y mensajes. Acceso total al panel.'),
(2, 'Usuario registrado', 'Consulta la información del panel y administra su propia cuenta. No puede modificar datos.');

-- ---------------------------------------------------------------------
-- 2. usuarios: rol, nombre, correo, estado y cambio de contraseña obligatorio
-- ---------------------------------------------------------------------
ALTER TABLE usuarios
    ADD COLUMN rol_id TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER id,
    ADD COLUMN nombre VARCHAR(100) NOT NULL DEFAULT '' AFTER rol_id,
    ADD COLUMN correo VARCHAR(150) NULL AFTER nombre_usuario,
    ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER contrasena,
    ADD COLUMN debe_cambiar_contrasena TINYINT(1) NOT NULL DEFAULT 1 AFTER activo,
    ADD COLUMN actualizado_en DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Los usuarios de antes eran administradores del panel.
UPDATE usuarios SET nombre = IF(nombre_usuario = 'admin', 'Administrador ValiNix', nombre_usuario) WHERE nombre = '';
UPDATE usuarios SET correo = CONCAT(LOWER(nombre_usuario), '@valinix.software') WHERE correo IS NULL;

ALTER TABLE usuarios
    MODIFY rol_id TINYINT UNSIGNED NOT NULL,
    MODIFY nombre VARCHAR(100) NOT NULL,
    MODIFY correo VARCHAR(150) NOT NULL,
    MODIFY debe_cambiar_contrasena TINYINT(1) NOT NULL DEFAULT 0,
    ADD UNIQUE KEY uq_usuarios_correo (correo),
    ADD KEY idx_usuarios_rol (rol_id),
    ADD CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles (id)
        ON UPDATE CASCADE ON DELETE RESTRICT;

-- ---------------------------------------------------------------------
-- 3. servicios: estado visible/oculto y nombres de icono nuevos
-- ---------------------------------------------------------------------
ALTER TABLE servicios ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1;

UPDATE servicios SET icono = CASE icono
    WHEN 'layout'    THEN 'service-web'
    WHEN 'grid'      THEN 'service-catalog'
    WHEN 'shield'    THEN 'service-shield'
    WHEN 'chat'      THEN 'service-support'
    WHEN 'briefcase' THEN 'service-business'
    ELSE icono
END;
UPDATE servicios SET icono = 'service-web'
WHERE icono NOT IN ('service-web', 'service-catalog', 'service-shield', 'service-support',
                    'service-landing', 'service-business', 'whatsapp-outline', 'service-seo');

ALTER TABLE servicios MODIFY icono VARCHAR(40) NOT NULL DEFAULT 'service-web';

-- ---------------------------------------------------------------------
-- 4. categorias (nueva) a partir de las categorías de texto de proyectos
-- ---------------------------------------------------------------------
CREATE TABLE categorias (
    id     TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug   VARCHAR(40)      NOT NULL COMMENT 'Identificador para la URL y los filtros',
    nombre VARCHAR(60)      NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categorias (slug, nombre) VALUES
('gastronomia', 'Gastronomía'),
('adopcion',    'Adopción animal'),
('social',      'Emprendimiento social'),
('landing',     'Landing Pages'),
('empresarial', 'Empresariales'),
('ecommerce',   'E-commerce'),
('catalogo',    'Catálogos');

-- Cualquier otra categoría que exista en proyectos también se conserva.
INSERT INTO categorias (slug, nombre)
SELECT DISTINCT p.categoria, CONCAT(UPPER(LEFT(p.categoria, 1)), SUBSTRING(p.categoria, 2))
FROM proyectos p
LEFT JOIN categorias c ON c.slug = p.categoria
WHERE c.id IS NULL AND p.categoria <> '';

-- ---------------------------------------------------------------------
-- 5. proyectos: categoria_id (FK), usuario_id (FK) y color numérico
-- ---------------------------------------------------------------------
ALTER TABLE proyectos
    ADD COLUMN categoria_id TINYINT UNSIGNED NULL AFTER id,
    ADD COLUMN usuario_id INT NULL AFTER categoria_id,
    ADD COLUMN color TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER enlace,
    ADD COLUMN actualizado_en DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

UPDATE proyectos p
INNER JOIN categorias c ON c.slug = p.categoria
SET p.categoria_id = c.id;

-- Proyectos sin categoría: quedan como "Empresariales".
UPDATE proyectos SET categoria_id = (SELECT id FROM categorias WHERE slug = 'empresarial') WHERE categoria_id IS NULL;

UPDATE proyectos SET color = LEAST(GREATEST(CAST(imagen_color AS UNSIGNED), 1), 6);
UPDATE proyectos SET usuario_id = (SELECT MIN(id) FROM usuarios WHERE rol_id = 1);
UPDATE proyectos SET enlace = '#' WHERE enlace IS NULL OR enlace = '';

ALTER TABLE proyectos
    DROP COLUMN categoria,
    DROP COLUMN imagen_color,
    MODIFY categoria_id TINYINT UNSIGNED NOT NULL,
    MODIFY enlace VARCHAR(255) NOT NULL DEFAULT '#',
    ADD KEY idx_proyectos_categoria (categoria_id),
    ADD KEY idx_proyectos_usuario (usuario_id),
    ADD CONSTRAINT fk_proyectos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    ADD CONSTRAINT fk_proyectos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    ADD CONSTRAINT chk_proyectos_color CHECK (color BETWEEN 1 AND 6);

-- ---------------------------------------------------------------------
-- 6. planes: clave (para /contacto?plan=...) y etiqueta del destacado
-- ---------------------------------------------------------------------
ALTER TABLE planes
    ADD COLUMN clave VARCHAR(20) NULL AFTER id,
    ADD COLUMN etiqueta VARCHAR(30) NULL DEFAULT NULL AFTER destacado;

UPDATE planes SET clave = CASE
    WHEN nombre LIKE '%sico%'       THEN 'basico'
    WHEN nombre LIKE '%rofesional%' THEN 'profesional'
    WHEN nombre LIKE '%remium%'     THEN 'premium'
    ELSE CONCAT('plan', id)
END;
UPDATE planes SET etiqueta = 'Más elegido' WHERE destacado = 1;

ALTER TABLE planes
    MODIFY clave VARCHAR(20) NOT NULL,
    ADD UNIQUE KEY uq_planes_clave (clave);

-- ---------------------------------------------------------------------
-- 7. testimonios: nombres de columna nuevos y calificación entre 1 y 5
-- ---------------------------------------------------------------------
UPDATE testimonios SET empresa_cliente = '' WHERE empresa_cliente IS NULL;
UPDATE testimonios SET calificacion = LEAST(GREATEST(COALESCE(calificacion, 5), 1), 5);

ALTER TABLE testimonios
    CHANGE nombre_cliente autor VARCHAR(120) NOT NULL,
    CHANGE empresa_cliente empresa VARCHAR(120) NOT NULL DEFAULT '',
    MODIFY calificacion TINYINT UNSIGNED NOT NULL DEFAULT 5,
    ADD CONSTRAINT chk_testimonios_calificacion CHECK (calificacion BETWEEN 1 AND 5);

-- ---------------------------------------------------------------------
-- 8. mensajes: asunto, servicio_id (FK) y plan_id (FK)
-- ---------------------------------------------------------------------
ALTER TABLE mensajes
    ADD COLUMN servicio_id INT NULL AFTER id,
    ADD COLUMN plan_id INT NULL AFTER servicio_id,
    ADD COLUMN asunto VARCHAR(150) NOT NULL DEFAULT '' AFTER telefono;

-- El "presupuesto" de antes era el plan: se convierte en plan_id.
UPDATE mensajes m
INNER JOIN planes pl ON pl.clave = m.presupuesto
SET m.plan_id = pl.id;

-- El "tipo de proyecto" de antes queda escrito en el asunto.
UPDATE mensajes SET asunto = CONCAT('Cotización: ', CASE tipo_proyecto
    WHEN 'landing'     THEN 'Landing page'
    WHEN 'empresarial' THEN 'Sitio empresarial'
    WHEN 'catalogo'    THEN 'Catálogo digital'
    WHEN 'ecommerce'   THEN 'E-commerce'
    WHEN 'otro'        THEN 'Otro'
    ELSE 'Solicitud de contacto'
END)
WHERE asunto = '';

UPDATE mensajes SET telefono = '' WHERE telefono IS NULL;

ALTER TABLE mensajes
    DROP COLUMN tipo_proyecto,
    DROP COLUMN presupuesto,
    MODIFY asunto VARCHAR(150) NOT NULL,
    ADD KEY idx_mensajes_servicio (servicio_id),
    ADD KEY idx_mensajes_plan (plan_id),
    ADD KEY idx_mensajes_leido (leido, creado_en),
    ADD CONSTRAINT fk_mensajes_servicio FOREIGN KEY (servicio_id) REFERENCES servicios (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    ADD CONSTRAINT fk_mensajes_plan FOREIGN KEY (plan_id) REFERENCES planes (id)
        ON UPDATE CASCADE ON DELETE SET NULL;

-- ---------------------------------------------------------------------
-- 9. intentos_login (nueva): protección contra fuerza bruta
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

-- Listo: 11 tablas. Revisa con:  SHOW TABLES;
