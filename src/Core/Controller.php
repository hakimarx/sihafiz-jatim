<?php

/**
 * Base Controller
 * ================
 * Parent class untuk semua controller
 */

class Controller
{
    /**
     * Render view dengan layout
     */
    protected function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        // Extract data ke variabel
        extract($data);

        // Buffer view content
        ob_start();
        $viewPath = VIEWS_PATH . '/' . str_replace('.', '/', $view) . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: {$view}";
        }

        $content = ob_get_clean();

        // Render with layout or just content
        if ($layout) {
            $layoutPath = VIEWS_PATH . '/layouts/' . $layout . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    /**
     * Redirect ke URL
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data dengan sanitization
     */
    protected function input(string $key, $default = null): mixed
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;

        if (is_string($value)) {
            return sanitize($value);
        }

        return $value;
    }

    /**
     * Validate CSRF
     */
    protected function validateCsrf(): bool
    {
        $token = $this->input('csrf_token');
        return validateCsrfToken($token);
    }

    /**
     * Check if request is POST
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
