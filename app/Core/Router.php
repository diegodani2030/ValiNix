<?php

declare(strict_types=1);

namespace App\Core;

use App\Controllers\ErrorController;

/**
 * Router mínimo: relaciona método HTTP + ruta con un método de un controlador.
 */
final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private $routes = ['GET' => [], 'POST' => []];

    /** @param array{0: class-string, 1: string} $handler */
    public function get(string $path, array $handler): self
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;

        return $this;
    }

    /** @param array{0: class-string, 1: string} $handler */
    public function post(string $path, array $handler): self
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;

        return $this;
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        $path    = $this->pathFromUri($uri);
        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            $allowed = $this->allowedMethods($path);

            if ($allowed !== []) {
                (new ErrorController())->methodNotAllowed($allowed);
                return;
            }

            (new ErrorController())->notFound();
            return;
        }

        [$class, $action] = $handler;
        (new $class())->$action();
    }

    /** Convierte la URI pedida en una ruta limpia, sin carpeta base ni "index.php". */
    private function pathFromUri(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $path = is_string($path) ? rawurldecode($path) : '/';

        $base = base_url();
        if ($base !== '' && strpos($path, $base . '/') === 0) {
            $path = substr($path, strlen($base));
        }

        $path = (string) preg_replace('#^/index\.php(?=/|$)#', '', $path);

        return $this->normalize($path);
    }

    private function normalize(string $path): string
    {
        return '/' . trim($path, '/');
    }

    /** @return string[] */
    private function allowedMethods(string $path): array
    {
        $allowed = [];
        foreach ($this->routes as $method => $routes) {
            if (isset($routes[$path])) {
                $allowed[] = $method;
            }
        }

        return $allowed;
    }
}
