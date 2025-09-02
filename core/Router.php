<?php
class Router {
    private array $routes = ['GET'=>[], 'POST'=>[]];

    public function get(string $path, $handler) { $this->routes['GET'][$this->normalize($path)] = $handler; }
    public function post(string $path, $handler) { $this->routes['POST'][$this->normalize($path)] = $handler; }

    private function normalize(string $path): string {
        $path = '/' . ltrim($path, '/');
        return rtrim($path, '/') ?: '/';
    }

    public function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $scriptName = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptName && str_starts_with($uri, $scriptName)) {
            $uri = substr($uri, strlen($scriptName));
        }
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = $this->normalize($uri);
        $handler = $this->routes[$method][$path] ?? null;
        if (!$handler) {
            http_response_code(404);
            echo "404 Not Found - {$path}";
            return;
        }
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler);
            $controller = new $class();
            return $controller->$method();
        }
        if (is_callable($handler)) return $handler();
        throw new Exception('Invalid route handler');
    }
}