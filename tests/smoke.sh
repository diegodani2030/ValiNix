#!/usr/bin/env bash
# Prueba de humo del sitio: levanta-lo antes con
#   php -S 127.0.0.1:8000 index.php
# y luego ejecuta:  bash tests/smoke.sh http://127.0.0.1:8000
set -euo pipefail

BASE="${1:-http://127.0.0.1:8000}"
TMP="$(mktemp -d)"
JAR="$TMP/cookies.txt"
PASS=0

fail() { echo "❌ FALLO: $*"; exit 1; }
ok()   { echo "✅ $*"; PASS=$((PASS + 1)); }

status() { curl -s -o /dev/null -w '%{http_code}' "$@"; }
count()  { { grep -oE "$1" "$2" || true; } | wc -l | tr -d ' '; }
fetch()  { curl -s -c "$JAR" -b "$JAR" -o "$2" "$1"; }   # guarda la página en un archivo (con cookies)
token()  { grep -oE 'name="_token" value="[a-f0-9]+"' "$1" | sed -E 's/.*value="([a-f0-9]+)"/\1/'; }
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
expect_count 'class="filter-btn' "$TMP/page_proyectos.html" 5 "filtros de proyectos"
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
grep -q '<option value="premium" selected>' "$TMP/plan.html" || fail "?plan=premium no preselecciona el presupuesto"
fetch "$BASE/contacto?plan=hackeo" "$TMP/plan-bad.html"
if grep -q ' selected>' "$TMP/plan-bad.html"; then fail "un ?plan inválido no debería preseleccionar nada"; fi
ok "contacto: formulario con CSRF y preselección de plan desde /precios"

# --- Archivos estáticos ---------------------------------------------------------
for f in css/style.css js/script.js assets/icons/favicon.svg; do
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

# --- Formulario: envío válido ---------------------------------------------------
fetch "$BASE/contacto" "$TMP/form.html"
TOKEN="$(token "$TMP/form.html")"
loc=$(curl -s -c "$JAR" -b "$JAR" -o /dev/null -w '%{http_code} %{redirect_url}' \
    --data-urlencode "_token=$TOKEN" \
    --data-urlencode "nombre=Ana Pérez" \
    --data-urlencode "empresa=Tienda Ana" \
    --data-urlencode "correo=ana@example.com" \
    --data-urlencode "telefono=+57 300 123 4567" \
    --data-urlencode "tipo_proyecto=landing" \
    --data-urlencode "presupuesto=basico" \
    --data-urlencode "mensaje=Quiero una landing page para mi tienda." \
    "$BASE/contacto")
[ "$loc" = "303 $BASE/contacto#contactForm" ] || fail "POST válido debería redirigir a /contacto#contactForm (fue: $loc)"
fetch "$BASE/contacto" "$TMP/after-ok.html"
grep -q 'form-alert--success' "$TMP/after-ok.html" || fail "no aparece el mensaje de éxito"
grep -q 'Gracias Ana Pérez' "$TMP/after-ok.html" || fail "el mensaje de éxito no saluda por el nombre"
fetch "$BASE/contacto" "$TMP/reload.html"
if grep -q 'form-alert' "$TMP/reload.html"; then fail "el mensaje flash debería desaparecer al recargar"; fi
ok "envío válido: redirección a /contacto, mensaje de éxito y flash de un solo uso"

# --- Formulario: datos inválidos ------------------------------------------------
TOKEN="$(token "$TMP/reload.html")"
curl -s -c "$JAR" -b "$JAR" -o /dev/null \
    --data-urlencode "_token=$TOKEN" \
    --data-urlencode "nombre=A" \
    --data-urlencode "correo=correo-malo" \
    --data-urlencode "telefono=abc" \
    --data-urlencode "presupuesto=premium" \
    --data-urlencode "mensaje=corto" \
    "$BASE/contacto"
fetch "$BASE/contacto" "$TMP/after-bad.html"
grep -q 'form-alert--error' "$TMP/after-bad.html" || fail "no aparece el mensaje de error"
grep -q 'El correo electrónico no es válido' "$TMP/after-bad.html" || fail "falta el error del correo"
grep -q 'value="correo-malo"' "$TMP/after-bad.html" || fail "no se conservaron los datos escritos"
grep -q '<option value="premium" selected>' "$TMP/after-bad.html" || fail "no se conservó el presupuesto elegido"
ok "datos inválidos: errores mostrados y formulario conserva lo escrito"

# --- Formulario: sin token CSRF -------------------------------------------------
curl -s -c "$JAR" -b "$JAR" -o /dev/null \
    --data-urlencode "nombre=Ana" --data-urlencode "correo=ana@example.com" \
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

echo
echo "Todas las pruebas pasaron ($PASS grupos)."
