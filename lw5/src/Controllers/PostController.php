<?php
namespace App\Controllers;

use App\Services\PostService;
use App\Services\UserService;

class PostController {
    public function __construct(
        private PostService $postService,
        private UserService $userService
    ) {}

    // Главная страница
    public function index(): void {
        try {
            $posts = $this->postService->getAll();
            $users = $this->userService->getAll();

            $this->render('home', [
                'posts' => $posts,
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }

    public function create(): void {
        try {
            $this->render('create');
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }
    
    public function edit(): void {
        try {
            $this->render('edit');
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