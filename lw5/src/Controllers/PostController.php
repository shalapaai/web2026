<?php
namespace App\Controllers;

use App\Services\PostService;
use App\Services\UserService;
use App\Core\BaseController;

class PostController extends BaseController {
    public function __construct(
        private PostService $postService,
        private UserService $userService
    ) {}

    // Главная страница
    public function home(): void {
        try {
            $postId = ($_GET['postId'] ?? 0);
            if ($postId) {
                $posts = [$this->postService->getById($postId)];
                $users = [$this->userService->getById($posts[0]->authorId)];
            } else {
                $posts = $this->postService->getAll();
                $users = $this->userService->getAll();
            }
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
            // $posts = $this->postService->getAll();
            // $users = $this->userService->getAll();
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method === 'POST') {
                $postData = $_POST;
                $uploadedImages = $this->postService->uploadImages($_FILES['images'] ?? null);
                $postData['uploadedImages'] = $uploadedImages;
                $authorId = $this->userService->getCurrentUserId();
                $this->postService->create($postData, $authorId);
            } else {
                $this->render('create', [
                    // 'posts' => $posts,
                    // 'users' => $users
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }
    
    public function edit(): void {
        try {
            // $posts = $this->postService->getAll();
            // $users = $this->userService->getAll();
            $this->render('edit', [
                // 'posts' => $posts,
                // 'users' => $users,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Ошибка: ' . $e->getMessage();
        }
    }
}
