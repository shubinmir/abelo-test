<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        // {slug} → именованная группа (?P<slug>[a-z0-9-]+)
        $regex = preg_replace('#\{(\w+)\}#', '(?P<$1>[a-z0-9-sl]+)', $pattern);

        $this->routes[] = [
            'regex'   => '#^' . $regex . '$#',
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): string
    {
        if ($method !== 'GET') {
            throw new NotFoundException();
        }

        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?: '/', '/') ?: '/';

        foreach ($this->routes as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return ($route['handler'])($params);
            }
        }

        throw new NotFoundException();
    }
}
