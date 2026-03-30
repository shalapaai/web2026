<?php
namespace App\Controllers;

use App\Services\PostService;
use App\Services\UserService;
use App\Core\BaseController;
use App\Models\Post;

class PostController extends BaseController {
    public function __construct(
        private PostService $postService,
        private UserService $userService
    ) {}

    // Главная страница
    public function home(): void {
        try {
            $posts = $this->postService->getAll();
            $users = $this->userService->getAll();
            $postId = (int)($_GET['postId'] ?? 0);
            
            if ($postId > 0) {
                $posts = array_filter($posts, fn(Post $p) => $p->id === $postId );
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
                $data = $_POST;
                $uploadedImages = $this->postService->uploadImages($_FILES['images'] ?? null);
                $data['uploadedImages'] = $uploadedImages;
                $authorId = 1;
                $this->postService->create($data, $authorId);
            }
            $this->render('create', [
                // 'posts' => $posts,
                // 'users' => $users
            ]);
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
