<?php
abstract class Controller {
    protected function view(string $template, array $data = [], string $layout = 'front') {
        extract($data);
        $view = new View();
        $view->render($template, $data, $layout);
    }
    protected function redirect(string $path) {
        header('Location: ' . base_url($path));
        exit;
    }
    protected function input(string $key, $default = null) {
        return htmlspecialchars(trim($_POST[$key] ?? $_GET[$key] ?? $default), ENT_QUOTES, 'UTF-8');
    }
}