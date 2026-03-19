<?php
namespace App\Controllers;

use App\Services\PostService;
use App\Services\UserService;

class UserController {
    public function __construct(
        private UserService $userService
    ) {}

    public function login(): void {
        try {
            $this->render('login');
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }

    public function profile(): void {
        try {
            $this->render('profile');
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }

    private function render(string $view, array $data = []): void {
        // Извлекаем переменные из массива
        extract($data);
        include __DIR__ . "/../../Views/pages/{$view}.php";
    }
}

?>