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

    public function renderProfile(): void {
        $userId = ($_GET['id'] ?? $this->getCurrentUser());
        $user = $this->userService->getUserById($userId);
        $posts = $this->postService->getPostsByAuthorId($user->id);
        $this->render('profile', [
            'posts' => $posts,
            'user' => $user,
        ]);
    }

    public function renderEditProfile(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_GET['id'] ?? '';
        $user = $this->userService->getUserById($userId);
        $this->render('editProfile', ['user' => $user]);
        return;
    }

    public function renderLogin(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->render('login', ['path' => '/login']);
        return;
    }

    public function renderRegister(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->render('login', ['path' => '/register']);
        return;
    }

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
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

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
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

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
        if (session_status() === PHP_SESSION_NONE) session_start();
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

    public function editProfile(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_GET['id'] ?? '';
        $user = $this->userService->getUserById($userId);
        
        try {
            $name = trim($_POST['name'] ?? '');
            $content = trim($_POST['profileStatus'] ?? '');
            $avatarPath = $user->avatar;
            
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatarPath = $this->userService->uploadAvatar($_FILES['avatar']);
            } elseif (!empty($_POST['existingAvatarPath'])) {
                $avatarPath = $_POST['existingAvatarPath'];
            }
            // Иначе оставляем $user->avatar (ничего не меняем)
            
            $updated = $this->userService->updateUser($userId, [
                'name' => $name,
                'profileStatus' => $content,
                'avatar' => $avatarPath
            ]);
            if (!$updated) {
                http_response_code(404);
                $this->sendJson(false, 'not_found', 'Пользователь не найден');
                return;
            }
            $this->sendJson(true, null, null, ['userId' => $userId]);
        } catch (\Exception $e) {
            error_log('Edit profile error: ' . $e->getMessage());
            http_response_code(500);
            $this->sendJson(false, 'server_error', 'Ошибка сервера');
        }
    }
}