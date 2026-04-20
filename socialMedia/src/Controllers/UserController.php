<?php
namespace App\Controllers;

use App\Services\PostService;
use App\Services\UserService;
use App\Core\BaseController;

class UserController extends BaseController {
    public function __construct(
        private PostService $postService,
        private UserService $userService        
    ) {}

    public function login(): void {
        try {
            // $users = $this->userService->getAll();
            $this->render('login', [
                // 'users' => $users
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }

    public function profile(): void {
        try {
            $userId = ($_GET['id'] ?? 0);
            $user = $this->userService->getUserById($userId);
            $posts = $this->postService->getPostsByAuthorId($user->id);
            $this->render('profile', [
                'posts' => $posts,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }
}