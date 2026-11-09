#!/usr/bin/env bash
# Prueba el sitio con una base de la versión anterior ya migrada:
#   1. importar tests/fixtures/valinix-version-anterior.sql
#   2. ejecutar database/migracion-version-anterior.sql
#   3. levantar el sitio apuntando a esa base y correr:
#        bash tests/migracion.sh http://127.0.0.1:8002
set -euo pipefail

BASE="${1:-http://127.0.0.1:8002}"
TMP="$(mktemp -d)"
JAR="$TMP/cookies.txt"

fail() { echo "❌ FALLO: $*"; exit 1; }
ok()   { echo "✅ $*"; }
get()  { curl -s -c "$JAR" -b "$JAR" -o "$2" -w '%{http_code}' "$BASE$1"; }
post() { local path="$1"; shift; curl -s -c "$JAR" -b "$JAR" -o "$TMP/post.html" -w '%{http_code} %{redirect_url}' "$@" "$BASE$path"; }
token() { grep -oE 'name="_token" value="[a-f0-9]+"' "$1" | head -1 | sed -E 's/.*value="([a-f0-9]+)"/\1/'; }

# --- Sitio público con los datos que ya existían -------------------------------
[ "$(get /servicios "$TMP/s.html")" = 200 ] || fail "/servicios no carga"
grep -q '<h3>Sistemas de información</h3>' "$TMP/s.html" || fail "/servicios no muestra los servicios de la base"
[ "$(get /proyectos "$TMP/p.html")" = 200 ] || fail "/proyectos no carga"
grep -q 'Raíces del Fogón' "$TMP/p.html" || fail "/proyectos no muestra los proyectos de la base"
grep -q 'data-filter="adopcion">Adopción animal' "$TMP/p.html" || fail "la categoría de texto 'adopcion' no se convirtió en filtro"
[ "$(get / "$TMP/h.html")" = 200 ] || fail "la home no carga"
grep -q 'María López' "$TMP/h.html" || fail "el testimonio existente no aparece (columnas renombradas)"
[ "$(get /precios "$TMP/pr.html")" = 200 ] || fail "/precios no carga"
grep -q 'href="/contacto?plan=profesional"' "$TMP/pr.html" || fail "los planes no recibieron su clave"
ok "sitio público: servicios, proyectos, filtros, testimonios y planes salen de la base migrada"

# --- Login con el usuario de antes ---------------------------------------------
get /login "$TMP/l.html" > /dev/null
r=$(post /login --data-urlencode "_token=$(token "$TMP/l.html")" --data-urlencode "usuario=admin" --data-urlencode "contrasena=valinix2026")
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "el admin de antes debería entrar y tener que cambiar la contraseña (fue: $r)"
get /admin/cuenta "$TMP/c.html" > /dev/null
TOK="$(token "$TMP/c.html")"
r=$(post /admin/cuenta --data-urlencode "_token=$TOK" --data-urlencode "nombre=Administrador ValiNix" --data-urlencode "correo=admin@valinix.software" \
    --data-urlencode "actual=valinix2026" --data-urlencode "contrasena=Migrada2026" --data-urlencode "confirmar=Migrada2026")
[ "$r" = "303 $BASE/admin/cuenta" ] || fail "no se pudo cambiar la contraseña después de migrar (fue: $r)"
ok "login: el usuario de antes entra (hash \$2b\$) y se le pide contraseña nueva"

# --- Panel con los datos de antes ----------------------------------------------
[ "$(get /admin/mensajes "$TMP/m.html")" = 200 ] || fail "/admin/mensajes no carga"
grep -q 'Cotización: Landing page' "$TMP/m.html" || fail "el tipo de proyecto del mensaje no quedó en el asunto"
grep -q 'Plan Profesional' "$TMP/m.html" || fail "el presupuesto del mensaje no se convirtió en plan"
grep -q 'cliente@example.com' "$TMP/m.html" || fail "el mensaje existente no aparece"
[ "$(get /admin/proyectos "$TMP/ap.html")" = 200 ] || fail "/admin/proyectos no carga"
grep -q 'Adopción animal' "$TMP/ap.html" || fail "el proyecto no muestra su categoría"
[ "$(get /admin/usuarios "$TMP/u.html")" = 200 ] || fail "/admin/usuarios no carga"
grep -q 'Administrador' "$TMP/u.html" || fail "el usuario de antes no quedó como Administrador"

r=$(post /admin/proyectos/guardar --data-urlencode "_token=$TOK" --data-urlencode "titulo=Proyecto tras migrar" \
    --data-urlencode "categoria_id=1" --data-urlencode "descripcion=Creado para comprobar el CRUD tras la migración." \
    --data-urlencode "enlace=#" --data-urlencode "color=2")
[ "$r" = "303 $BASE/admin/proyectos" ] || fail "no se pudo crear un proyecto en la base migrada (fue: $r)"
get /proyectos "$TMP/p2.html" > /dev/null
grep -q 'Proyecto tras migrar' "$TMP/p2.html" || fail "el proyecto nuevo no aparece en /proyectos"
ok "panel: mensajes, proyectos y usuarios de antes visibles; el CRUD funciona sobre la base migrada"

echo
echo "La migración funciona."
