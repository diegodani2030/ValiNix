<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DatabaseException;

/**
 * Contenido dinámico del sitio público.
 *
 * Lee de la base de datos (servicios, proyectos, planes, testimonios, FAQ) y
 * lo entrega con el formato que usan las vistas. Si la base de datos no está
 * configurada o no responde, usa el contenido de respaldo de SiteContent para
 * que el sitio nunca se caiga.
 */
final class Contenido
{
    /** @return array<int, array<string, mixed>> */
    public static function services(): array
    {
        return self::intentar('services', static function (): array {
            return array_map(static function (array $s): array {
                return ['id' => (int) $s['id'], 'icon' => $s['icono'], 'title' => $s['titulo'], 'text' => $s['descripcion']];
            }, (new Servicio())->publicos());
        }, SiteContent::services());
    }

    /** @return array<int, array<string, mixed>> */
    public static function projects(): array
    {
        return self::intentar('projects', static function (): array {
            return array_map(static function (array $p): array {
                return [
                    'thumb'    => (int) $p['color'],
                    'category' => $p['categoria_slug'],
                    'tag'      => $p['categoria'],
                    'title'    => $p['titulo'],
                    'text'     => $p['descripcion'],
                    'url'      => $p['enlace'],
                ];
            }, (new Proyecto())->todos());
        }, SiteContent::projects());
    }

    /** Filtros del portafolio: solo categorías que tienen proyectos. @return array<string, string> */
    public static function projectFilters(): array
    {
        return self::intentar('projectFilters', static function (): array {
            $filtros = ['all' => 'Todos'];
            foreach ((new Categoria())->conProyectos() as $categoria) {
                $filtros[(string) $categoria['slug']] = (string) $categoria['nombre'];
            }

            return $filtros;
        }, SiteContent::projectFilters());
    }

    /** @return array<int, array<string, mixed>> */
    public static function plans(): array
    {
        return self::intentar('plans', static function (): array {
            return array_map(static function (array $p): array {
                return [
                    'key'      => $p['clave'],
                    'name'     => $p['nombre'],
                    'desc'     => $p['descripcion'],
                    'price'    => $p['precio'],
                    'featured' => $p['destacado'],
                    'badge'    => $p['etiqueta'],
                    'features' => $p['caracteristicas'],
                ];
            }, (new Plan())->conCaracteristicas());
        }, SiteContent::plans());
    }

    /** @return array<int, array<string, mixed>> */
    public static function testimonials(): array
    {
        return self::intentar('testimonials', static function (): array {
            return array_map(static function (array $t): array {
                return ['rating' => (int) $t['calificacion'], 'quote' => $t['comentario'], 'author' => $t['autor'], 'company' => $t['empresa']];
            }, (new Testimonio())->todos());
        }, SiteContent::testimonials());
    }

    /** @return array<int, array<string, string>> */
    public static function faqs(): array
    {
        return self::intentar('faqs', static function (): array {
            return array_map(static function (array $f): array {
                return ['q' => $f['pregunta'], 'a' => $f['respuesta']];
            }, (new Faq())->todas());
        }, SiteContent::faqs());
    }

    /** Opciones del campo "Servicio de interés": id => título. @return array<int, string> */
    public static function serviceOptions(): array
    {
        $opciones = [];
        foreach (self::services() as $service) {
            $opciones[(int) $service['id']] = (string) $service['title'];
        }

        return $opciones;
    }

    /** Opciones del campo "Plan de interés": clave => nombre. @return array<string, string> */
    public static function planOptions(): array
    {
        $opciones = ['' => 'Selecciona un plan'];
        foreach (self::plans() as $plan) {
            $opciones[(string) $plan['key']] = (string) $plan['name'];
        }
        $opciones['no-seguro'] = 'Aún no lo sé';

        return $opciones;
    }

    /**
     * Ejecuta la consulta una sola vez por petición; si la base de datos falla, devuelve el respaldo.
     *
     * @param mixed $respaldo
     * @return mixed
     */
    private static function intentar(string $clave, callable $consulta, $respaldo)
    {
        static $cache = [];

        if (!array_key_exists($clave, $cache)) {
            try {
                $cache[$clave] = $consulta();
            } catch (DatabaseException $exception) {
                $cache[$clave] = $respaldo;
            } catch (\PDOException $exception) {
                error_log("ValiNix: error de consulta ($clave), se usa el contenido de respaldo: " . $exception->getMessage());
                $cache[$clave] = $respaldo;
            }
        }

        return $cache[$clave];
    }
}
