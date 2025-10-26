<?php

namespace App\Core;

/**
 * Simple Router
 */
class Router
{
    private array $routes = [];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $path, string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'pattern' => $this->convertToPattern($path)
        ];
    }

    private function convertToPattern(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(): Response
    {
        $method = $this->request->method();
        $path = $this->request->path();

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path, $matches)) {
                // Extract parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return $this->callHandler($route['handler'], $params);
            }
        }

        // 404 Not Found
        return new Response('404 Not Found', 404);
    }

    private function callHandler(string $handler, array $params): Response
    {
        [$class, $method] = explode('@', $handler);

        if (!class_exists($class)) {
            throw new \Exception("Controller $class not found");
        }

        $controller = new $class();

        if (!method_exists($controller, $method)) {
            throw new \Exception("Method $method not found in controller $class");
        }

        $result = $controller->$method($this->request, ...$params);

        if ($result instanceof Response) {
            return $result;
        }

        if (is_string($result)) {
            return new Response($result);
        }

        if (is_array($result)) {
            return new Response(json_encode($result), 200, ['Content-Type' => 'application/json']);
        }

        return new Response('', 204);
    }
}
