<?php

/**
 * Simple Router for PHP Native MVC
 * =================================
 * Single Entry Point Routing System
 */

class Router
{
    private array $routes = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
    }

    /**
     * Register a GET route
     */
    public function get(string $path, callable|array $handler): self
    {
        $this->routes['GET'][$path] = $handler;
        return $this;
    }

    /**
     * Register a POST route
     */
    public function post(string $path, callable|array $handler): self
    {
        $this->routes['POST'][$path] = $handler;
        return $this;
    }

    /**
     * Run the router
     */
    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getUri();

        // Check for matching route
        foreach ($this->routes[$method] ?? [] as $path => $handler) {
            $pattern = $this->pathToPattern($path);

            if (preg_match($pattern, $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $this->executeHandler($handler, $params);
                return;
            }
        }

        // 404 Not Found
        $this->notFound();
    }

    /**
     * Get clean URI
     */
    private function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Auto-detect base path from APP_URL
        if (defined('APP_URL')) {
            $parsedUrl = parse_url(APP_URL);
            $basePath = $parsedUrl['path'] ?? '';
            if ($basePath && strpos($uri, $basePath) === 0) {
                $uri = substr($uri, strlen($basePath));
            }
        }

        // Remove base path
        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }

        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        return '/' . trim($uri ?: '/', '/');
    }

    /**
     * Convert route path to regex pattern
     */
    private function pathToPattern(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute route handler
     */
    private function executeHandler(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            call_user_func_array([$controller, $method], $params);
        } else {
            call_user_func_array($handler, $params);
        }
    }

    /**
     * 404 handler
     */
    private function notFound(): void
    {
        http_response_code(404);
        echo '<h1>404 - Halaman Tidak Ditemukan</h1>';
        echo '<p>Maaf, halaman yang Anda cari tidak tersedia.</p>';
        echo '<a href="' . APP_URL . '">Kembali ke Beranda</a>';
    }
}
