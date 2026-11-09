#!/usr/bin/env bash
# Prueba de humo del sitio: levántalo antes con
#   php -S 127.0.0.1:8000 index.php
# y luego ejecuta:
#   bash tests/smoke.sh http://127.0.0.1:8000 db     (con base de datos importada)
#   bash tests/smoke.sh http://127.0.0.1:8000 nodb   (sin base de datos: contenido de respaldo)
set -euo pipefail

BASE="${1:-http://127.0.0.1:8000}"
MODE="${2:-db}"
TMP="$(mktemp -d)"
JAR="$TMP/cookies.txt"
PASS=0

fail() { echo "❌ FALLO: $*"; exit 1; }
ok()   { echo "✅ $*"; PASS=$((PASS + 1)); }

status() { curl -s -o /dev/null -w '%{http_code}' "$@"; }
count()  { { grep -oE "$1" "$2" || true; } | wc -l | tr -d ' '; }
fetch()  { curl -s -c "$JAR" -b "$JAR" -o "$2" "$1"; }   # guarda la página en un archivo (con cookies)
token()  { grep -oE 'name="_token" value="[a-f0-9]+"' "$1" | head -1 | sed -E 's/.*value="([a-f0-9]+)"/\1/'; }
expect_count() { # patrón archivo cantidad descripción
    local n; n="$(count "$1" "$2")"
    [ "$n" = "$3" ] || fail "$4: se esperaban $3 y hay $n"
}

# --- Cada página del menú es una página aparte ----------------------------------
# ruta | título esperado | texto del menú activo
PAGES=(
    "/|ValiNix — Diseño y desarrollo web|Inicio"
    "/nosotros|Nosotros — ValiNix|Nosotros"
    "/servicios|Servicios — ValiNix|Servicios"
    "/proceso|Cómo trabajamos — ValiNix|Proceso"
    "/proyectos|Proyectos — ValiNix|Proyectos"
    "/precios|Precios y planes — ValiNix|Precios"
    "/preguntas-frecuentes|Preguntas frecuentes — ValiNix|Preguntas frecuentes"
    "/contacto|Contacto — ValiNix|Contacto"
    "/manual|Manual de usuario — ValiNix|-"
)
for entry in "${PAGES[@]}"; do
    IFS='|' read -r path title nav <<< "$entry"
    file="$TMP/page$(echo "$path" | tr '/' '_').html"
    code=$(curl -s -c "$JAR" -b "$JAR" -o "$file" -w '%{http_code}' "$BASE$path")
    [ "$code" = 200 ] || fail "$path devolvió $code"
    grep -q "<title>$title" "$file" || fail "$path no tiene el título '$title'"
    [ "$(head -c 15 "$file")" = '<!DOCTYPE html>' ] || fail "$path no empieza con <!DOCTYPE html>"
    expect_count '<h1[ >]' "$file" 1 "$path debería tener un solo <h1>"
    if [ "$nav" = "-" ]; then   # página fuera del menú: nada marcado
        expect_count 'class="is-active" aria-current="page">' "$file" 0 "$path no está en el menú y no debería marcar nada"
    else
        expect_count 'class="is-active" aria-current="page">' "$file" 1 "$path debería marcar una sola página activa en el menú"
        grep -q "class=\"is-active\" aria-current=\"page\">$nav</a>" "$file" || fail "$path no marca '$nav' como activo en el menú"
    fi
    if grep -qE 'href="#(inicio|servicios|proceso|proyectos|precios|faq|contacto)"' "$file"; then
        fail "$path aún enlaza a secciones con # en vez de páginas"
    fi
done
ok "las 9 páginas responden 200, con su título, un <h1> y el menú activo correcto"

P="$TMP/page_.html"
NAV="$(sed -n '/<nav class="navbar__nav"/,/<\/nav>/p' "$P")"
for href in /nosotros /servicios /proceso /proyectos /precios /preguntas-frecuentes /contacto; do
    echo "$NAV" | grep -q "href=\"$href\"" || fail "el menú no enlaza a $href"
done
if echo "$NAV" | grep -q 'href="/manual"'; then fail "el manual no debería estar en el menú principal"; fi
grep -q 'href="/manual"' "$P" || fail "el footer debería enlazar al manual"
ok "el menú enlaza a cada página (el manual va en el footer)"

# --- Contenido de cada página ---------------------------------------------------
grep -q 'class="hero"' "$P" || fail "la home no tiene el hero"
grep -q 'id="problema"' "$P" || fail "la home no tiene la sección del problema"
expect_count 'class="service-card explore-card' "$P" 6 "tarjetas de 'Explora' en la home"
expect_count 'class="testimonial-slide[ "]' "$P" 3 "testimonios en la home"
if grep -q 'class="services"' "$P"; then fail "la home ya no debería mostrar la sección completa de servicios"; fi
grep -q 'href="/contacto" class="btn btn--accent">Quiero mi página web' "$P" || fail "el botón del hero no lleva a /contacto"
ok "inicio: hero, problema, 6 accesos a páginas y testimonios"

expect_count 'class="service-card reveal"' "$TMP/page_servicios.html" 6 "servicios"
grep -q 'id="beneficios"' "$TMP/page_servicios.html" || fail "/servicios no muestra los beneficios"
expect_count 'class="timeline__step' "$TMP/page_proceso.html" 5 "pasos del proceso"
expect_count 'class="project-card"' "$TMP/page_proyectos.html" 6 "proyectos"
expect_count 'class="filter-btn' "$TMP/page_proyectos.html" 7 "filtros de proyectos"
grep -q 'Raíces del Fogón' "$TMP/page_proyectos.html" || fail "/proyectos no muestra Raíces del Fogón"
grep -q 'Rincón Tinto Animal' "$TMP/page_proyectos.html" || fail "/proyectos no muestra Rincón Tinto Animal"
for s in 'Desarrollo web' 'Sistemas de información' 'Seguridad informática' 'Soporte y mantenimiento'; do
    grep -q "<h3>$s</h3>" "$TMP/page_servicios.html" || fail "/servicios no muestra $s (servicio del manual)"
done
expect_count 'class="price-card[ "]' "$TMP/page_precios.html" 3 "planes"
grep -q 'price-card--featured' "$TMP/page_precios.html" || fail "falta el plan destacado"
for plan in basico profesional premium; do
    grep -q "href=\"/contacto?plan=$plan\"" "$TMP/page_precios.html" || fail "el plan $plan no enlaza a /contacto?plan=$plan"
done
expect_count 'class="faq-item"' "$TMP/page_preguntas-frecuentes.html" 7 "preguntas frecuentes"
ok "servicios, proceso, proyectos, precios y FAQ muestran su contenido"

N="$TMP/page_nosotros.html"
grep -q 'Creatividad que atrapa, <span class="text-accent">diseño que destaca.</span>' "$N" || fail "/nosotros no tiene el título con acento"
grep -q '<h3>Misión</h3>' "$N" || fail "/nosotros no muestra la misión"
grep -q '<h3>Visión</h3>' "$N" || fail "/nosotros no muestra la visión"
expect_count 'class="valor-card reveal"' "$N" 5 "valores en /nosotros"
grep -q 'href="/manual" class="btn btn--ghost">Ver manual de usuario' "$N" || fail "/nosotros no enlaza al manual"
grep -q '¿Quieres crear algo con nosotros?' "$N" || fail "/nosotros no tiene su llamado a la acción propio"
ok "nosotros: misión, visión, 5 valores, enlace al manual y CTA propio"

M="$TMP/page_manual.html"
PDF="$(grep -oE 'src="/assets/docs/manual-usuario-valinix\.pdf\?v=[0-9]+#view=FitH"' "$M" | head -1)"
[ -n "$PDF" ] || fail "/manual no incrusta el PDF en un iframe"
grep -q 'download="Manual_de_Usuario_ValiNix.pdf" class="btn btn--accent">Descargar PDF' "$M"     || grep -qE 'class="btn btn--accent" download="Manual_de_Usuario_ValiNix.pdf">Descargar PDF' "$M" || fail "/manual no tiene el botón Descargar PDF"
grep -q 'target="_blank" rel="noopener">Abrir en otra pestaña' "$M" || fail "/manual no tiene el botón para abrir en otra pestaña"
grep -q 'class="manual__fallback"' "$M" || fail "/manual no tiene el enlace alternativo de descarga"
ctype=$(curl -s -o "$TMP/manual.pdf" -w '%{http_code} %{content_type}' "$BASE/assets/docs/manual-usuario-valinix.pdf")
case "$ctype" in "200 application/pdf"*) ;; *) fail "el PDF no se sirve bien (fue: $ctype)";; esac
[ "$(head -c 5 "$TMP/manual.pdf")" = '%PDF-' ] || fail "el archivo del manual no es un PDF"
ok "manual: PDF incrustado, botones de descarga/abrir y el archivo se sirve como application/pdf"

C="$TMP/page_contacto.html"
grep -q 'id="contactForm"' "$C" || fail "/contacto no tiene el formulario"
grep -q 'action="/contacto"' "$C" || fail "el formulario no apunta a /contacto"
[ -n "$(token "$C")" ] || fail "el formulario no tiene token CSRF"
fetch "$BASE/contacto?plan=premium" "$TMP/plan.html"
grep -q '<option value="premium" selected>' "$TMP/plan.html" || fail "?plan=premium no preselecciona el plan"
for field in nombre correo telefono asunto mensaje servicio plan; do
    grep -q "name=\"$field\"" "$C" || fail "el formulario no tiene el campo $field"
done
fetch "$BASE/contacto?plan=hackeo" "$TMP/plan-bad.html"
if grep -q ' selected>' "$TMP/plan-bad.html"; then fail "un ?plan inválido no debería preseleccionar nada"; fi
ok "contacto: formulario con CSRF y preselección de plan desde /precios"

# --- Archivos estáticos ---------------------------------------------------------
for f in css/style.css js/script.js assets/icons/favicon.png; do
    [ "$(status "$BASE/$f")" = 200 ] || fail "$f no carga"
done
curl -s -o "$TMP/style.css" "$BASE/css/style.css"
grep -q '\.page-header' "$TMP/style.css" || fail "style.css no tiene los estilos de páginas internas"
grep -qE 'href="/css/style\.css\?v=[0-9]+"' "$P" || fail "style.css no lleva ?v= para evitar la caché vieja"
grep -qE 'src="/js/script\.js\?v=[0-9]+"' "$P" || fail "script.js no lleva ?v= para evitar la caché vieja"
ok "css, js y favicon cargan, con versión para evitar caché vieja"

# --- Errores y rutas privadas ---------------------------------------------------
[ "$(status "$BASE/no-existe")" = 404 ] || fail "/no-existe debería ser 404"
[ "$(status "$BASE/app/routes.php")" = 404 ] || fail "/app/ no debería ser accesible"
[ "$(status "$BASE/config/config.php")" = 404 ] || fail "/config/ no debería ser accesible"
[ "$(status "$BASE/servicios/")" = 200 ] || fail "/servicios/ (con barra final) debería funcionar"
ok "404 para rutas inexistentes y carpetas privadas; la barra final se tolera"

# --- Formulario de contacto: datos inválidos -----------------------------------
fetch "$BASE/contacto" "$TMP/form.html"
TOKEN="$(token "$TMP/form.html")"
curl -s -c "$JAR" -b "$JAR" -o /dev/null \
    --data-urlencode "_token=$TOKEN" \
    --data-urlencode "nombre=A" \
    --data-urlencode "correo=correo-malo" \
    --data-urlencode "telefono=abc" \
    --data-urlencode "asunto=" \
    --data-urlencode "servicio=999" \
    --data-urlencode "plan=premium" \
    --data-urlencode "mensaje=corto" \
    "$BASE/contacto"
fetch "$BASE/contacto" "$TMP/after-bad.html"
grep -q 'form-alert--error' "$TMP/after-bad.html" || fail "no aparece el mensaje de error"
grep -q 'El correo electrónico no es válido' "$TMP/after-bad.html" || fail "falta el error del correo"
grep -q 'El campo Asunto es obligatorio' "$TMP/after-bad.html" || fail "falta el error de campo obligatorio (Asunto)"
grep -q 'El servicio seleccionado no es válido' "$TMP/after-bad.html" || fail "un servicio inexistente debería rechazarse"
grep -q 'value="correo-malo"' "$TMP/after-bad.html" || fail "no se conservaron los datos escritos"
grep -q '<option value="premium" selected>' "$TMP/after-bad.html" || fail "no se conservó el plan elegido"
ok "contacto con datos inválidos: errores de validación y formulario conserva lo escrito"

# --- Formulario: sin token CSRF -------------------------------------------------
curl -s -c "$JAR" -b "$JAR" -o /dev/null \
    --data-urlencode "nombre=Ana" --data-urlencode "correo=ana@example.com" --data-urlencode "asunto=Hola" \
    --data-urlencode "telefono=3001234567" --data-urlencode "mensaje=Mensaje suficientemente largo" \
    "$BASE/contacto"
fetch "$BASE/contacto" "$TMP/no-token.html"
grep -q 'Tu sesión expiró' "$TMP/no-token.html" || fail "un POST sin token debería rechazarse"
ok "POST sin token CSRF rechazado"

# --- Formulario: bot (honeypot) -------------------------------------------------
curl -s -c "$JAR" -b "$JAR" -o /dev/null --data-urlencode "website=http://spam.example" "$BASE/contacto"
fetch "$BASE/contacto" "$TMP/bot.html"
grep -q 'Gracias, hemos recibido tu mensaje.' "$TMP/bot.html" || fail "el honeypot no respondió como esperado"
ok "honeypot descarta bots en silencio"

# --- Formulario: envío válido ---------------------------------------------------
send_contact() { # jar asunto
    local tok
    fetch "$BASE/contacto" "$TMP/form2.html"
    tok="$(token "$TMP/form2.html")"
    curl -s -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code} %{redirect_url}' \
        --data-urlencode "_token=$tok" \
        --data-urlencode "nombre=Ana Perez" \
        --data-urlencode "empresa=Tienda Ana" \
        --data-urlencode "correo=ana@example.com" \
        --data-urlencode "telefono=+57 300 123 4567" \
        --data-urlencode "asunto=$1" \
        --data-urlencode "servicio=5" \
        --data-urlencode "plan=basico" \
        --data-urlencode "mensaje=Quiero una landing page para mi tienda." \
        "$BASE/contacto"
}

ASUNTO="Landing para tienda $RANDOM"
loc="$(send_contact "$ASUNTO")"
[ "$loc" = "303 $BASE/contacto#contactForm" ] || fail "POST válido debería redirigir a /contacto#contactForm (fue: $loc)"
fetch "$BASE/contacto" "$TMP/after-send.html"

if [ "$MODE" = "nodb" ]; then
    # Sin base de datos (y sin correo en el servidor de pruebas) no se puede registrar: hay que decirlo.
    grep -q 'Error al guardar' "$TMP/after-send.html" || fail "sin base de datos debería avisar 'Error al guardar'"
    grep -q 'value="Ana Perez"' "$TMP/after-send.html" || fail "sin base de datos debería conservar lo escrito"
    ok "sin base de datos: el contacto avisa que no se pudo guardar (ya no finge éxito)"

    code=$(curl -s -o "$TMP/admin-nodb.html" -w '%{http_code}' "$BASE/admin")
    [ "$code" = 503 ] || fail "/admin sin base de datos debería ser 503 (fue $code)"
    grep -q 'El panel no está disponible' "$TMP/admin-nodb.html" || fail "falta el aviso de panel no disponible"
    fetch "$BASE/login" "$TMP/login-nodb.html"
    grep -q 'aún no está conectado a la base de datos' "$TMP/login-nodb.html" || fail "/login sin base de datos debería avisarlo"
    ok "sin base de datos: panel 503 y aviso en /login; el sitio público usa el contenido de respaldo"

    echo
    echo "Todas las pruebas pasaron ($PASS grupos, modo sin base de datos)."
    exit 0
fi

grep -q 'form-alert--success' "$TMP/after-send.html" || fail "no aparece el mensaje de éxito"
grep -q 'Gracias Ana Perez' "$TMP/after-send.html" || fail "el mensaje de éxito no saluda por el nombre"
fetch "$BASE/contacto" "$TMP/reload.html"
if grep -q 'form-alert' "$TMP/reload.html"; then fail "el mensaje flash debería desaparecer al recargar"; fi
ok "envío válido: se guarda, redirige a /contacto con mensaje de éxito de un solo uso"

# =============================================================================
#  Panel administrativo (requiere base de datos)
# =============================================================================
AJAR="$TMP/admin.txt"
UJAR="$TMP/user.txt"
afetch() { curl -s -c "$1" -b "$1" -o "$3" -w '%{http_code}' "$BASE$2"; }        # jar ruta archivo → código
apost()  { local jar="$1" path="$2"; shift 2; curl -s -c "$jar" -b "$jar" -o "$TMP/post.html" -w '%{http_code} %{redirect_url}' "$@" "$BASE$path"; }
login()  { # jar usuario contraseña → código y redirección
    afetch "$1" /login "$TMP/login.html" > /dev/null
    apost "$1" /login --data-urlencode "_token=$(token "$TMP/login.html")" --data-urlencode "usuario=$2" --data-urlencode "contrasena=$3"
}
atoken() { afetch "$1" /admin/cuenta "$TMP/tok.html" > /dev/null; token "$TMP/tok.html"; }

# --- Acceso sin sesión ----------------------------------------------------------
for path in /admin /admin/proyectos /admin/usuarios /admin/mensajes; do
    r=$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE$path")
    [ "$r" = "303 $BASE/login" ] || fail "$path sin sesión debería mandar a /login (fue: $r)"
done
grep -q 'href="/login" class="btn btn--ghost btn--sm navbar__login"' "$P" || fail "el menú no tiene el botón Iniciar sesión"
ok "el panel exige iniciar sesión y el menú tiene el botón Iniciar sesión"

# --- Login ----------------------------------------------------------------------
r=$(login "$AJAR" admin clave-equivocada)
[ "$r" = "303 $BASE/login" ] || fail "login fallido debería volver a /login (fue: $r)"
afetch "$AJAR" /login "$TMP/login-bad.html" > /dev/null
grep -q 'Usuario o contraseña incorrectos.' "$TMP/login-bad.html" || fail "falta el mensaje 'Usuario o contraseña incorrectos.'"
grep -q 'value="admin"' "$TMP/login-bad.html" || fail "el login fallido debería conservar el usuario escrito"
r=$(apost "$AJAR" /login --data-urlencode "usuario=admin" --data-urlencode "contrasena=valinix2026")
[ "$r" = "303 $BASE/login" ] || fail "login sin token CSRF debería rechazarse (fue: $r)"
r=$(login "$AJAR" admin@valinix.software valinix2026)
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "con la contraseña inicial el login debería llevar a Mi cuenta (fue: $r)"
afetch "$AJAR" /admin/cuenta "$TMP/cuenta.html" > /dev/null
grep -q 'Bienvenido, Administrador ValiNix.' "$TMP/cuenta.html" || fail "falta el saludo después de iniciar sesión"
grep -q 'elige una contraseña nueva' "$TMP/cuenta.html" || fail "falta el aviso de cambiar la contraseña inicial"
r=$(curl -s -c "$AJAR" -b "$AJAR" -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE/admin/proyectos")
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "sin cambiar la contraseña inicial no debería poder usar el panel (fue: $r)"
TOK=$(atoken "$AJAR")
r=$(apost "$AJAR" /admin/cuenta --data-urlencode "_token=$TOK" --data-urlencode "nombre=Administrador ValiNix" --data-urlencode "correo=admin@valinix.software")
[ "$r" = "422 " ] || fail "Mi cuenta sin contraseña nueva debería rechazarse mientras sea obligatorio (fue: $r)"
r=$(apost "$AJAR" /admin/cuenta --data-urlencode "_token=$TOK" --data-urlencode "nombre=Administrador ValiNix" --data-urlencode "correo=admin@valinix.software" \
    --data-urlencode "actual=valinix2026" --data-urlencode "contrasena=AdminPrueba2026" --data-urlencode "confirmar=AdminPrueba2026")
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "cambiar la contraseña inicial debería funcionar (fue: $r)"
[ "$(afetch "$AJAR" /admin "$TMP/dash.html")" = 200 ] || fail "/admin no carga después de cambiar la contraseña"
if grep -q 'elige una contraseña nueva' "$TMP/dash.html"; then fail "el aviso debería desaparecer al cambiar la contraseña"; fi
grep -q 'Mensajes por servicio' "$TMP/dash.html" || fail "el administrador debería ver el resumen de mensajes"
ok "login: error con datos malos, rechaza sin CSRF, entra con usuario o correo y obliga a cambiar la contraseña inicial"

# --- Bloqueo por intentos fallidos (guardado en la base de datos) --------------
BJAR="$TMP/bloqueo.txt"
for i in 1 2 3 4 5; do login "$BJAR" "bloqueo-$$" mala > /dev/null; rm -f "$BJAR"; done
login "$BJAR" "bloqueo-$$" mala > /dev/null
afetch "$BJAR" /login "$TMP/locked.html" > /dev/null
grep -q 'Demasiados intentos fallidos' "$TMP/locked.html" || fail "5 intentos fallidos deberían bloquear el usuario aunque se borre la cookie"
ok "bloqueo tras 5 intentos fallidos, guardado en el servidor (borrar la cookie no lo reinicia)"

# --- Mensajes del contacto (consulta multitabla) --------------------------------
afetch "$AJAR" /admin/mensajes "$TMP/msgs.html" > /dev/null
grep -q "$ASUNTO" "$TMP/msgs.html" || fail "el mensaje enviado desde /contacto no aparece en el panel"
grep -q 'Landing pages' "$TMP/msgs.html" || fail "el mensaje no muestra el servicio (JOIN con servicios)"
grep -q 'Plan Básico' "$TMP/msgs.html" || fail "el mensaje no muestra el plan (JOIN con planes)"
MID=$(grep -oE '/admin/mensajes/ver\?id=[0-9]+' "$TMP/msgs.html" | head -1 | grep -oE '[0-9]+$')
afetch "$AJAR" "/admin/mensajes?buscar=no-existe-xyz" "$TMP/msgs-empty.html" > /dev/null
grep -q 'No hay mensajes que coincidan' "$TMP/msgs-empty.html" || fail "el buscador de mensajes no filtra"
afetch "$AJAR" "/admin/mensajes/ver?id=$MID" "$TMP/msg.html" > /dev/null
grep -q 'ana@example.com' "$TMP/msg.html" || fail "el detalle del mensaje no muestra el correo"
afetch "$AJAR" "/admin/mensajes?estado=leidos" "$TMP/msgs-read.html" > /dev/null
grep -q "$ASUNTO" "$TMP/msgs-read.html" || fail "al abrir el mensaje debería quedar como leído"
ok "mensajes: lo enviado en /contacto llega al panel con servicio y plan; buscar, ver y marcar leído"

# --- CRUD de proyectos ----------------------------------------------------------
r=$(apost "$AJAR" /admin/proyectos/guardar --data-urlencode "_token=$TOK" --data-urlencode "titulo=" \
    --data-urlencode "categoria_id=99" --data-urlencode "descripcion=corta" --data-urlencode "enlace=javascript:alert(1)" --data-urlencode "color=9")
[ "$r" = "422 " ] || fail "un proyecto inválido debería responder 422 (fue: $r)"
for msg in 'El campo Título es obligatorio.' 'El valor elegido en Categoría no es válido.' 'debe tener al menos 10 caracteres' 'debe ser un enlace que empiece por http' 'debe ser un número entero entre 1 y 6'; do
    grep -q "$msg" "$TMP/post.html" || fail "falta la validación: $msg"
done

TITULO="Proyecto de prueba $RANDOM"
r=$(apost "$AJAR" /admin/proyectos/guardar --data-urlencode "_token=$TOK" --data-urlencode "titulo=$TITULO" \
    --data-urlencode "categoria_id=3" --data-urlencode "descripcion=Proyecto creado por la prueba automática." \
    --data-urlencode "enlace=https://example.com" --data-urlencode "color=2")
[ "$r" = "303 $BASE/admin/proyectos" ] || fail "crear proyecto debería redirigir a la lista (fue: $r)"
afetch "$AJAR" /admin/proyectos "$TMP/proys.html" > /dev/null
grep -q 'Registro guardado correctamente.' "$TMP/proys.html" || fail "falta 'Registro guardado correctamente.'"
grep -q "$TITULO" "$TMP/proys.html" || fail "el proyecto creado no aparece en el panel"
fetch "$BASE/proyectos" "$TMP/pub.html"
grep -q "$TITULO" "$TMP/pub.html" || fail "el proyecto creado no aparece en /proyectos"
PID=$(grep -B30 "$TITULO" "$TMP/proys.html" | grep -oE '<td>[0-9]+</td>' | tail -1 | grep -oE '[0-9]+')
[ -n "$PID" ] || fail "no se encontró el id del proyecto creado"

afetch "$AJAR" "/admin/proyectos/editar?id=$PID" "$TMP/edit.html" > /dev/null
grep -q "value=\"$TITULO\"" "$TMP/edit.html" || fail "el formulario de edición no trae los datos"
r=$(apost "$AJAR" /admin/proyectos/actualizar --data-urlencode "_token=$TOK" --data-urlencode "id=$PID" --data-urlencode "titulo=$TITULO editado" \
    --data-urlencode "categoria_id=4" --data-urlencode "descripcion=Proyecto editado por la prueba automática." \
    --data-urlencode "enlace=#" --data-urlencode "color=3")
[ "$r" = "303 $BASE/admin/proyectos" ] || fail "actualizar proyecto debería redirigir (fue: $r)"
afetch "$AJAR" "/admin/proyectos?categoria=4" "$TMP/proys-cat.html" > /dev/null
grep -q 'Registro actualizado correctamente.' "$TMP/proys-cat.html" || fail "falta 'Registro actualizado correctamente.'"
grep -q "$TITULO editado" "$TMP/proys-cat.html" || fail "el filtro por categoría no muestra el proyecto editado"

r=$(apost "$AJAR" /admin/proyectos/eliminar --data-urlencode "id=$PID")
[ "$r" = "303 $BASE/admin" ] || fail "eliminar sin token CSRF debería rechazarse (fue: $r)"
r=$(apost "$AJAR" /admin/proyectos/eliminar --data-urlencode "_token=$TOK" --data-urlencode "id=$PID")
[ "$r" = "303 $BASE/admin/proyectos" ] || fail "eliminar proyecto debería redirigir (fue: $r)"
afetch "$AJAR" /admin/proyectos "$TMP/proys2.html" > /dev/null
grep -q 'Registro eliminado correctamente.' "$TMP/proys2.html" || fail "falta 'Registro eliminado correctamente.'"
if grep -q "$TITULO" "$TMP/proys2.html"; then fail "el proyecto eliminado sigue en la lista"; fi
ok "CRUD de proyectos: validación del servidor (422), crear, consultar, filtrar, editar y eliminar"

# --- Servicios y testimonios ----------------------------------------------------
r=$(apost "$AJAR" /admin/servicios/guardar --data-urlencode "_token=$TOK" --data-urlencode "titulo=Desarrollo web" \
    --data-urlencode "descripcion=Un servicio duplicado." --data-urlencode "icono=service-web" --data-urlencode "orden=1" --data-urlencode "activo=1")
[ "$r" = "422 " ] || fail "un servicio con título repetido debería responder 422 (fue: $r)"
grep -q 'Ya existe un servicio con ese título.' "$TMP/post.html" || fail "falta la validación de título repetido"
r=$(apost "$AJAR" /admin/servicios/guardar --data-urlencode "_token=$TOK" --data-urlencode "titulo=Servicio oculto $RANDOM" \
    --data-urlencode "descripcion=Servicio de prueba que no se muestra." --data-urlencode "icono=service-seo" --data-urlencode "orden=50")
[ "$r" = "303 $BASE/admin/servicios" ] || fail "crear servicio debería redirigir (fue: $r)"
fetch "$BASE/servicios" "$TMP/serv-pub.html"
if grep -q 'Servicio oculto' "$TMP/serv-pub.html"; then fail "un servicio inactivo no debería verse en /servicios"; fi
r=$(apost "$AJAR" /admin/testimonios/guardar --data-urlencode "_token=$TOK" --data-urlencode "autor=Cliente Prueba" \
    --data-urlencode "empresa=Empresa X" --data-urlencode "calificacion=7" --data-urlencode "comentario=Muy buen servicio." --data-urlencode "orden=1")
[ "$r" = "422 " ] || fail "una calificación de 7 debería rechazarse (fue: $r)"
ok "servicios y testimonios: validan duplicados y rangos; los servicios ocultos no salen en el sitio"

# --- Gestión de usuarios y permisos ---------------------------------------------
NUEVO="prueba$RANDOM"
r=$(apost "$AJAR" /admin/usuarios/guardar --data-urlencode "_token=$TOK" --data-urlencode "nombre=Usuario de Prueba" \
    --data-urlencode "nombre_usuario=con espacios" --data-urlencode "correo=admin@valinix.software" --data-urlencode "rol_id=2" \
    --data-urlencode "contrasena=corta" --data-urlencode "confirmar=otra")
[ "$r" = "422 " ] || fail "un usuario inválido debería responder 422 (fue: $r)"
for msg in 'solo admite letras, números' 'Ese correo ya está registrado.' 'debe tener al menos 8 caracteres'; do
    grep -q "$msg" "$TMP/post.html" || fail "falta la validación de usuario: $msg"
done
r=$(apost "$AJAR" /admin/usuarios/guardar --data-urlencode "_token=$TOK" --data-urlencode "nombre=Usuario de Prueba" \
    --data-urlencode "nombre_usuario=$NUEVO" --data-urlencode "correo=$NUEVO@example.com" --data-urlencode "rol_id=2" \
    --data-urlencode "activo=1" --data-urlencode "contrasena=ClaveSegura123" --data-urlencode "confirmar=ClaveSegura123")
[ "$r" = "303 $BASE/admin/usuarios" ] || fail "crear usuario debería redirigir (fue: $r)"
afetch "$AJAR" /admin/usuarios "$TMP/users.html" > /dev/null
grep -q "$NUEVO" "$TMP/users.html" || fail "el usuario creado no aparece"
grep -q 'Usuario registrado' "$TMP/users.html" || fail "la lista no muestra el rol (JOIN con roles)"
UID_NUEVO=$(grep -A10 "<td>$NUEVO</td>" "$TMP/users.html" | grep -oE 'name="id" value="[0-9]+"' | head -1 | grep -oE '[0-9]+')
[ -n "$UID_NUEVO" ] || fail "no se encontró el id del usuario creado"

r=$(apost "$AJAR" /admin/usuarios/eliminar --data-urlencode "_token=$TOK" --data-urlencode "id=1")
afetch "$AJAR" /admin/usuarios "$TMP/users2.html" > /dev/null
grep -q 'No puedes eliminar tu propia cuenta' "$TMP/users2.html" || fail "el administrador no debería poder eliminarse a sí mismo"

r=$(login "$UJAR" "$NUEVO" ClaveSegura123)
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "un usuario creado por el administrador debe cambiar su contraseña al entrar (fue: $r)"
UTOK=$(atoken "$UJAR")
r=$(apost "$UJAR" /admin/cuenta --data-urlencode "_token=$UTOK" --data-urlencode "nombre=Usuario de Prueba" --data-urlencode "correo=$NUEVO@example.com" \
    --data-urlencode "actual=equivocada" --data-urlencode "contrasena=OtraClave456" --data-urlencode "confirmar=OtraClave456")
grep -q 'La contraseña actual no es correcta.' "$TMP/post.html" || fail "Mi cuenta debería pedir la contraseña actual correcta"
r=$(apost "$UJAR" /admin/cuenta --data-urlencode "_token=$UTOK" --data-urlencode "nombre=Usuario Cambiado" --data-urlencode "correo=$NUEVO@example.com" \
    --data-urlencode "actual=ClaveSegura123" --data-urlencode "contrasena=OtraClave456" --data-urlencode "confirmar=OtraClave456")
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "cambiar la contraseña debería redirigir (fue: $r)"
[ "$(afetch "$UJAR" /admin/proyectos "$TMP/u-proys.html")" = 200 ] || fail "el usuario registrado debería poder consultar proyectos"
if grep -q 'Nuevo proyecto' "$TMP/u-proys.html"; then fail "el usuario registrado no debería ver el botón de crear"; fi
for path in /admin/usuarios /admin/mensajes /admin/proyectos/nuevo; do
    [ "$(afetch "$UJAR" "$path" "$TMP/u-deny.html")" = 403 ] || fail "el usuario registrado no debería entrar a $path"
done
r=$(apost "$UJAR" /admin/proyectos/eliminar --data-urlencode "_token=$UTOK" --data-urlencode "id=1")
[ "${r%% *}" = 403 ] || fail "el usuario registrado no debería poder eliminar (fue: $r)"
afetch "$UJAR" /admin "$TMP/u-dash.html" > /dev/null
if grep -q 'Mensajes por servicio' "$TMP/u-dash.html"; then fail "el usuario registrado no debería ver los mensajes"; fi

r=$(apost "$UJAR" /logout --data-urlencode "_token=$UTOK")
[ "$r" = "303 $BASE/login" ] || fail "cerrar sesión debería volver a /login (fue: $r)"
r=$(curl -s -c "$UJAR" -b "$UJAR" -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE/admin")
[ "$r" = "303 $BASE/login" ] || fail "después de cerrar sesión /admin debería pedir login (fue: $r)"
r=$(login "$UJAR" "$NUEVO" OtraClave456)
[ "$r" = "303 $BASE/admin" ] || fail "la contraseña nueva debería funcionar (fue: $r)"

r=$(apost "$AJAR" /admin/usuarios/eliminar --data-urlencode "_token=$TOK" --data-urlencode "id=$UID_NUEVO")
[ "$r" = "303 $BASE/admin/usuarios" ] || fail "eliminar usuario debería redirigir (fue: $r)"
r=$(curl -s -c "$UJAR" -b "$UJAR" -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE/admin")
[ "$r" = "303 $BASE/login" ] || fail "un usuario eliminado debería perder la sesión (fue: $r)"
ok "usuarios: validación, roles y permisos, Mi cuenta, cerrar sesión y no auto-eliminarse"

echo
echo "Todas las pruebas pasaron ($PASS grupos, modo con base de datos)."
