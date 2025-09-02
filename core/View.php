<?php
class View {
    public function render(string $template, array $data, string $layout) {
        $contentPath = __DIR__ . '/../app/views/' . $template . '.php';
        $layoutPath = __DIR__ . '/../app/views/layouts/' . $layout . '.php';
        if (!file_exists($contentPath)) { echo "View not found: $contentPath"; return; }
        if (!file_exists($layoutPath)) { echo "Layout not found: $layoutPath"; return; }
        extract($data);
        ob_start();
        include $contentPath;
        $content = ob_get_clean();
        include $layoutPath;
    }
    public static function partial(string $name, array $data = []) {
        $path = __DIR__ . '/../app/views/partials/' . $name . '.php';
        if (file_exists($path)) {
            extract($data);
            include $path;
        }
    }
}