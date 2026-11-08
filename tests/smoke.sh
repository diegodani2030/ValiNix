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
count()  { grep -oE "$1" "$2" | wc -l | tr -d ' '; }
fetch()  { curl -s -c "$JAR" -b "$JAR" -o "$2" "$1"; }   # guarda la página en un archivo (con cookies)
token()  { grep -oE 'name="_token" value="[a-f0-9]+"' "$1" | sed -E 's/.*value="([a-f0-9]+)"/\1/'; }

# --- Página principal ---------------------------------------------------------
code=$(curl -s -c "$JAR" -b "$JAR" -o "$TMP/home.html" -w '%{http_code}' "$BASE/")
[ "$code" = 200 ] || fail "la home devolvió $code"
grep -q '<title>ValiNix' "$TMP/home.html" || fail "la home no tiene el título de ValiNix"
ok "home responde 200 con título"

for id in inicio problema servicios beneficios proceso proyectos precios testimonios faq contacto; do
    grep -q "id=\"$id\"" "$TMP/home.html" || fail "falta la sección #$id"
done
ok "están las 10 secciones con id"

[ "$(count 'class="service-card ' "$TMP/home.html")" = 6 ]  || fail "deberían ser 6 servicios"
[ "$(count 'class="project-card"' "$TMP/home.html")" = 6 ]  || fail "deberían ser 6 proyectos"
[ "$(count 'class="price-card[ "]' "$TMP/home.html")" = 3 ] || fail "deberían ser 3 planes"
[ "$(count 'class="faq-item"' "$TMP/home.html")" = 7 ]      || fail "deberían ser 7 preguntas"
[ "$(count 'class="testimonial-slide' "$TMP/home.html")" = 3 ] || fail "deberían ser 3 testimonios"
grep -q 'price-card--featured' "$TMP/home.html" || fail "falta el plan destacado"
ok "contenido del modelo renderizado (servicios, proyectos, planes, FAQ, testimonios)"

[ "$(head -c 15 "$TMP/home.html")" = '<!DOCTYPE html>' ] || fail "la página no empieza con <!DOCTYPE html>"
ok "sin caracteres sueltos antes del DOCTYPE"

TOKEN="$(token "$TMP/home.html")"
[ -n "$TOKEN" ] || fail "el formulario no tiene token CSRF"
grep -q 'action="/contacto"' "$TMP/home.html" || fail "el formulario no apunta a /contacto"
ok "formulario con token CSRF y action /contacto"

# --- Archivos estáticos ---------------------------------------------------------
for f in css/style.css js/script.js assets/icons/favicon.svg; do
    [ "$(status "$BASE/$f")" = 200 ] || fail "$f no carga"
done
curl -s -o "$TMP/style.css" "$BASE/css/style.css"
grep -q '{' "$TMP/style.css" || fail "style.css no parece CSS"
ok "css, js y favicon cargan"

# --- Errores y rutas privadas ---------------------------------------------------
[ "$(status "$BASE/no-existe")" = 404 ] || fail "/no-existe debería ser 404"
[ "$(status "$BASE/app/routes.php")" = 404 ] || fail "/app/ no debería ser accesible"
[ "$(status "$BASE/config/config.php")" = 404 ] || fail "/config/ no debería ser accesible"
ok "404 para rutas inexistentes y carpetas privadas"

loc=$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "$BASE/contacto")
[ "$loc" = "303 $BASE/#contacto" ] || fail "GET /contacto debería redirigir a /#contacto (fue: $loc)"
ok "GET /contacto redirige a la sección de contacto"

# --- Formulario: envío válido ---------------------------------------------------
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
[ "$loc" = "303 $BASE/#contacto" ] || fail "POST válido debería redirigir con 303 (fue: $loc)"
fetch "$BASE/" "$TMP/after-ok.html"
grep -q 'form-alert--success' "$TMP/after-ok.html" || fail "no aparece el mensaje de éxito"
grep -q 'Gracias Ana Pérez' "$TMP/after-ok.html" || fail "el mensaje de éxito no saluda por el nombre"
fetch "$BASE/" "$TMP/reload.html"
if grep -q 'form-alert' "$TMP/reload.html"; then fail "el mensaje flash debería desaparecer al recargar"; fi
ok "envío válido: redirección, mensaje de éxito y flash de un solo uso"

# --- Formulario: datos inválidos ------------------------------------------------
TOKEN="$(token "$TMP/after-ok.html")"
curl -s -c "$JAR" -b "$JAR" -o /dev/null \
    --data-urlencode "_token=$TOKEN" \
    --data-urlencode "nombre=A" \
    --data-urlencode "correo=correo-malo" \
    --data-urlencode "telefono=abc" \
    --data-urlencode "presupuesto=" \
    --data-urlencode "mensaje=corto" \
    "$BASE/contacto"
fetch "$BASE/" "$TMP/after-bad.html"
grep -q 'form-alert--error' "$TMP/after-bad.html" || fail "no aparece el mensaje de error"
grep -q 'El correo electrónico no es válido' "$TMP/after-bad.html" || fail "falta el error del correo"
grep -q 'value="correo-malo"' "$TMP/after-bad.html" || fail "no se conservaron los datos escritos"
ok "datos inválidos: errores mostrados y formulario conserva lo escrito"

# --- Formulario: sin token CSRF -------------------------------------------------
curl -s -c "$JAR" -b "$JAR" -o /dev/null \
    --data-urlencode "nombre=Ana" --data-urlencode "correo=ana@example.com" \
    --data-urlencode "telefono=3001234567" --data-urlencode "mensaje=Mensaje suficientemente largo" \
    "$BASE/contacto"
fetch "$BASE/" "$TMP/no-token.html"
grep -q 'Tu sesión expiró' "$TMP/no-token.html" || fail "un POST sin token debería rechazarse"
ok "POST sin token CSRF rechazado"

# --- Formulario: bot (honeypot) -------------------------------------------------
curl -s -c "$JAR" -b "$JAR" -o /dev/null --data-urlencode "website=http://spam.example" "$BASE/contacto"
fetch "$BASE/" "$TMP/bot.html"
grep -q 'Gracias, hemos recibido tu mensaje.' "$TMP/bot.html" || fail "el honeypot no respondió como esperado"
ok "honeypot descarta bots en silencio"

echo
echo "Todas las pruebas pasaron ($PASS grupos)."
