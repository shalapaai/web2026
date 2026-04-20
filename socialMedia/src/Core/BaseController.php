<?php
namespace App\Core;

abstract class BaseController {
    protected function render(string $view, array $data = []): void {
        extract($data);        
        include __DIR__ . "/../Views/pages/{$view}.php";
    }
}