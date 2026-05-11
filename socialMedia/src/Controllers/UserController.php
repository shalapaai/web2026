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

    public function getUserList(): void {
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

    public function getUserById(string $id): void {
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
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('login', ['path' => '/login']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $this->sendJson(false, 'missing_fields', 'Заполните все поля');
            return;
        }

        $user = $this->userService->getUserByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            $this->sendJson(false, 'invalid_credentials', 'Неверный email или пароль');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['is_logged'] = true;

        $this->sendJson(true, null, null, ['userId' => $user->id]);
    }

    public function register(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('login', ['path' => '/register']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            $this->sendJson(false, 'missing_fields', 'Заполните все поля');
            return;
        }

        if ($this->userService->getUserByEmail($email)) {
            $this->sendJson(false, 'user_exists', 'Этот email уже зарегистрирован');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->userService->createUser($email, $hashedPassword);

        if (!$user) {
            $this->sendJson(false, 'db_error', 'Ошибка при создании аккаунта');
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        
        $_SESSION['is_logged'] = true;

        $this->sendJson(true, null, null, ['userId' => $user->id]);
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '',            
                time() - 42000,  // время в прошлом -> удаление
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function profile(): void {
        try {
            $userId = ($_GET['id'] ?? $this->getCurrentUser());
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