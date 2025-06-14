<?php

namespace Perry\Helpers\Laravel;

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RouteObject;

class LaravelRouteFinder
{
    public static function findRealRoute(string $method, string $url): ?string
    {
        $method = strtolower($method);

        $url = parse_url($url, PHP_URL_PATH);

        /** @var RouteObject $route */
        foreach (Route::getRoutes() as $route) {
            $routeMethods = array_map('strtolower', $route->methods());

            if (!in_array($method, $routeMethods)) {
                continue;
            }

            $pattern = preg_replace('#\{[^/]+\}#', '[^/]+', $route->uri());
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, ltrim($url, '/'))) {
                return '/' . ltrim($route->uri(), '/');
            }
        }

        return null;
    }
}