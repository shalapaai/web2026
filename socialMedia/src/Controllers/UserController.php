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

    public function getUserList() {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        try {
            $users = $this->userService->getAllUserList();
            echo json_encode([
                'success' => true,
                'data' => $users,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);   
            echo json_encode([
                'success' => false,
                'error' => ['message' => $e->getMessage()]
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getUser($id) {

        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        try {
            $user = $this->userService->getUserById($id);
            echo json_encode([
                'success' => true,
                'data' => $user,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            http_response_code(500);   
            echo json_encode([
                'success' => false,
                'error' => ['message' => $e->getMessage()]
            ], JSON_UNESCAPED_UNICODE);
        }
    }

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