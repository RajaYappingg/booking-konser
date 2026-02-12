<?php

declare(strict_types=1);

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $path, $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, $handler): void
    {
        $pattern = $this->compilePattern($path, $paramNames);
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'paramNames' => $paramNames,
            'handler' => $handler,
        ];
    }

    private function compilePattern(string $path, ?array &$paramNames = []): string
    {
        $paramNames = [];
        $path = trim($path, '/');
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', function ($matches) use (&$paramNames) {
            $paramNames[] = $matches[1];
            return '([^/]+)';
        }, $path);

        return '#^' . $pattern . '$#';
    }

    public function dispatch(string $url): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $url = trim($url, '/');

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['pattern'], $url, $matches)) {
                array_shift($matches);
                $params = $this->mapParams($route['paramNames'], $matches);
                $this->invoke($route['handler'], $params);
                return;
            }
        }

        http_response_code(404);
        view('errors/404', ['title' => 'Page Not Found']);
    }

    private function mapParams(array $names, array $values): array
    {
        $params = [];
        foreach ($names as $index => $name) {
            $params[$name] = $values[$index] ?? null;
        }
        return $params;
    }

    private function invoke($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controllerName, $method] = explode('@', $handler, 2);
            $filePath = BASE_PATH . '/app/controllers/' . $controllerName . '.php';

            if (!class_exists($controllerName)) {
                if (file_exists($filePath)) {
                    require $filePath;
                }
            }

            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }

        http_response_code(500);
        echo 'Route handler not found.';
    }
}
