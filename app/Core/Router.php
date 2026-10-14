<?php

declare(strict_types=1);

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, callable $action): void {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, callable $action): void {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH);

        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo '404 - Page non trouvée';
            return;
        }

        $action = $this->routes[$method][$path];
        $action();
    }
}