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

    public function renderHome(): void {
        $this->render('home', []);
    }

    public function renderCreate(): void {
        $this->render('create', []);
        return;
    }

    public function renderEdit(): void {
        $this->render('edit', []);
        return;
    }

    public function getPostList(): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        try {
            $posts = $this->postService->getAllPostList();
            echo json_encode([
                'success' => true,
                'data' => $posts,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);   
            echo json_encode([
                'success' => false,
                'error' => ['message' => $e->getMessage()]
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getPost(string $id): void {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        try {
            $post = $this->postService->getPostById($id);
            echo json_encode([
                'success' => true,
                'data' => $post,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);   
            echo json_encode([
                'success' => false,
                'error' => ['message' => $e->getMessage()]
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function create(): void {
        try {
            $content = trim($_POST['content'] ?? '');
            $uploadedImages = $this->postService->uploadImages($_FILES['images'] ?? null);
            if (!$content) {
                http_response_code(400);
                $this->sendJson(false, 'missing_content', 'Введите текст поста');
                return;
            }
            if (empty($uploadedImages)) {
                http_response_code(400);
                $this->sendJson(false, 'no_images', 'Добавьте хотя бы одно фото');
                return;
            }
            if (session_status() === PHP_SESSION_NONE) session_start();
            $authorId = $_SESSION['user_id'] ?? null;
            if (!$authorId) {
                http_response_code(401);
                $this->sendJson(false, 'unauthorized', 'Требуется авторизация');
                return;
            }
            $postData = [
                'content' => $content,
                'uploadedImages' => $uploadedImages
            ];
            $postId = $this->postService->createPost($postData, $authorId);
            $this->sendJson(true, null, null, ['postId' => $postId]);
        } catch (\Exception $e) {
            error_log('Create post error: ' . $e->getMessage());
            http_response_code(500);
            $this->sendJson(false, 'server_error', 'Ошибка сервера');
        }
    }
    
    public function edit(string $id): void {
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $authorId = $_SESSION['user_id'] ?? null;
            
            if (!$authorId) {
                http_response_code(401);
                $this->sendJson(false, 'unauthorized', 'Требуется авторизация');
                return;
            }
            $content = trim($_POST['content'] ?? '');
            $existingImagePaths = json_decode($_POST['existing_images'] ?? '[]', true) ?? [];
            $removedImagePaths = json_decode($_POST['removed_images'] ?? '[]', true) ?? [];
            $newImages = $this->postService->uploadImages($_FILES['images'] ?? null);
            
            $hasImages = !empty($existingImagePaths) || !empty($newImages);
            if (!$content && !$hasImages) {
                http_response_code(400);
                $this->sendJson(false, 'missing_content', 'Введите текст или добавьте фото');
                return;
            }
            
            $postData = [
                'id' => $id,  
                'content' => $content,
                'newImages' => $newImages,              
                'existingImagePaths' => $existingImagePaths,
                'removedImagePaths' => $removedImagePaths   
            ];
            $updated = $this->postService->updatePost($postData, $authorId);
            if (!$updated) {
                http_response_code(404);
                $this->sendJson(false, 'not_found', 'Пост не найден');
                return;
            }
            $this->sendJson(true, null, null, ['postId' => $id]);
        } catch (\Exception $e) {
            error_log('Edit post error: ' . $e->getMessage());
            http_response_code(500);
            $this->sendJson(false, 'server_error', 'Ошибка сервера');
        }
    }

    public function toggleLike(string $postId) {
        $postLikes = ($this->postService->getPostById($postId))->likes;
        $userId = $_SESSION['user_id'];
        try {
            $isLiked = $this->postService->findLike($userId, $postId);
            if (!$isLiked) {
                $postLikes++;
                $this->postService->addLike($userId, $postId, $postLikes);
            } else {
                $postLikes--;
                $this->postService->removeLike($userId, $postId, $postLikes);
            }
            $this->sendJson(true, null, null, ['postId' => $postId]);
        } catch (\Exception $e) {
            http_response_code(500);   
            echo json_encode([
                'success' => false,
                'error' => ['message' => $e->getMessage()]
            ], JSON_UNESCAPED_UNICODE);
        }
        
    }

    public function isLiked(string $postId) {
        $userId = $_SESSION['user_id'];
        try {
            $isLiked = $this->postService->findLike($userId, $postId);
            $this->sendJson(true, null, null, ['isLiked' => $isLiked]);
        } catch (\Exception $e) {
            http_response_code(500);   
            echo json_encode([
                'success' => false,
                'error' => ['message' => $e->getMessage()]
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
