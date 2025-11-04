<?php
namespace App;


// Fonctions basiques rencontrées généralement dans un framework MVC classique en PHP
class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'layout/base'): void
    {

        extract($data);
        ob_start();

        require __DIR__ . '/../Views/' . $view . '.phtml';
        $content = ob_get_clean();

        if ($layout) {
            require __DIR__ . '/../Views/' . $layout . '.phtml';
        } else {
            echo $content;
        }
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function getPostData(): array
    {
        return filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) ?? [];
    }

    protected function getQueryParams(): array
    {
        return filter_input_array(INPUT_GET, FILTER_SANITIZE_SPECIAL_CHARS) ?? [];
    }
}
