-- =====================================================================
--  ValiNix — Consultas de ejemplo (DDL y DML)
--
--  Sirven para practicar y para la sustentación. Se pueden ejecutar en
--  phpMyAdmin (pestaña "SQL") después de importar valinix.sql.
--  Al lado de cada una dice en qué parte del sistema se usa.
-- =====================================================================


-- ---------------------------------------------------------------------
-- 1. Consultas GENERALES (traen todos los registros de una tabla)
-- ---------------------------------------------------------------------

-- Todos los servicios activos, en orden (página /servicios → Servicio::publicos)
SELECT id, titulo, descripcion, icono
FROM servicios
WHERE activo = 1
ORDER BY orden, id;

-- Todas las preguntas frecuentes (página /preguntas-frecuentes → Faq::todas)
SELECT pregunta, respuesta FROM faqs ORDER BY orden, id;


-- ---------------------------------------------------------------------
-- 2. Consultas ESPECÍFICAS (filtran por una condición)
-- ---------------------------------------------------------------------

-- Un proyecto por su id (formulario "Editar proyecto" → Proyecto::buscar)
SELECT * FROM proyectos WHERE id = 1;

-- Inicio de sesión: buscar por usuario O por correo (Usuario::buscarParaLogin)
SELECT * FROM usuarios
WHERE (nombre_usuario = 'admin' OR correo = 'admin')
  AND activo = 1
LIMIT 1;

-- Mensajes sin leer (contador del panel → Mensaje::contarNoLeidos)
SELECT COUNT(*) AS sin_leer FROM mensajes WHERE leido = 0;

-- Buscar mensajes por texto (buscador de /admin/mensajes → Mensaje::listar)
SELECT id, nombre, correo, asunto, creado_en
FROM mensajes
WHERE nombre LIKE '%ana%' OR correo LIKE '%ana%' OR asunto LIKE '%ana%'
ORDER BY creado_en DESC;


-- ---------------------------------------------------------------------
-- 3. Consultas MULTITABLA (JOIN)
-- ---------------------------------------------------------------------

-- Proyectos con el nombre de su categoría y de quién los registró
-- (página /proyectos y /admin/proyectos → Proyecto::todos)
SELECT p.id, p.titulo, p.descripcion, p.enlace,
       c.slug   AS categoria_slug,
       c.nombre AS categoria,
       u.nombre AS registrado_por
FROM proyectos p
INNER JOIN categorias c ON c.id = p.categoria_id
LEFT  JOIN usuarios   u ON u.id = p.usuario_id
ORDER BY p.id;

-- Usuarios con su rol (/admin/usuarios → Usuario::todos)
SELECT u.id, u.nombre, u.nombre_usuario, u.correo, r.nombre AS rol, u.activo
FROM usuarios u
INNER JOIN roles r ON r.id = u.rol_id
ORDER BY u.nombre;

-- Planes con sus características (página /precios → Plan::conCaracteristicas)
SELECT pl.clave, pl.nombre, pl.precio, pc.texto AS caracteristica
FROM planes pl
LEFT JOIN plan_caracteristicas pc ON pc.plan_id = pl.id
ORDER BY pl.orden, pc.orden;

-- Mensajes con el servicio y el plan que eligió el cliente (Mensaje::listar)
SELECT m.id, m.nombre, m.asunto, s.titulo AS servicio, pl.nombre AS plan, m.leido
FROM mensajes m
LEFT JOIN servicios s  ON s.id  = m.servicio_id
LEFT JOIN planes    pl ON pl.id = m.plan_id
ORDER BY m.creado_en DESC;

-- Categorías que tienen proyectos, con cuántos tiene cada una
-- (filtros de /proyectos y gráfico del panel → Categoria::conProyectos)
SELECT c.slug, c.nombre, COUNT(p.id) AS total
FROM categorias c
INNER JOIN proyectos p ON p.categoria_id = c.id
GROUP BY c.id, c.slug, c.nombre
HAVING COUNT(p.id) > 0
ORDER BY c.id;

-- Cuántos mensajes llegaron por cada servicio (panel → Mensaje::porServicio)
SELECT COALESCE(s.titulo, 'Sin servicio') AS servicio, COUNT(m.id) AS total
FROM mensajes m
LEFT JOIN servicios s ON s.id = m.servicio_id
GROUP BY s.id, s.titulo
ORDER BY total DESC;

-- Subconsulta: servicios que nadie ha pedido todavía
SELECT titulo FROM servicios
WHERE id NOT IN (SELECT servicio_id FROM mensajes WHERE servicio_id IS NOT NULL);


-- ---------------------------------------------------------------------
-- 4. DML: INSERT, UPDATE y DELETE (lo que hace el panel por dentro)
-- ---------------------------------------------------------------------

-- Crear un proyecto (Proyecto::crear)
INSERT INTO proyectos (categoria_id, usuario_id, titulo, descripcion, enlace, color)
VALUES (3, 1, 'Proyecto de prueba', 'Descripción del proyecto de prueba.', '#', 2);

-- Editarlo (Proyecto::actualizar)
UPDATE proyectos
SET titulo = 'Proyecto de prueba (editado)', color = 3
WHERE titulo = 'Proyecto de prueba';

-- Marcar un mensaje como leído (Mensaje::marcarLeido)
UPDATE mensajes SET leido = 1 WHERE id = 1;

-- Borrarlo (Proyecto::eliminar)
DELETE FROM proyectos WHERE titulo = 'Proyecto de prueba (editado)';


-- ---------------------------------------------------------------------
-- 5. DDL: cambiar la estructura (ejemplos; no hacen falta para el sistema)
-- ---------------------------------------------------------------------

-- Agregar una columna y luego quitarla
ALTER TABLE servicios ADD COLUMN precio_desde VARCHAR(30) NULL AFTER descripcion;
ALTER TABLE servicios DROP COLUMN precio_desde;

-- Crear un índice para buscar mensajes por correo más rápido, y borrarlo
CREATE INDEX idx_mensajes_correo ON mensajes (correo);
DROP INDEX idx_mensajes_correo ON mensajes;

-- Vista con el resumen de proyectos por categoría
CREATE OR REPLACE VIEW vista_proyectos_por_categoria AS
SELECT c.nombre AS categoria, COUNT(p.id) AS total
FROM categorias c
LEFT JOIN proyectos p ON p.categoria_id = c.id
GROUP BY c.id, c.nombre;

SELECT * FROM vista_proyectos_por_categoria;
DROP VIEW vista_proyectos_por_categoria;
