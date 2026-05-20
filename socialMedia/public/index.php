<?php
namespace App;

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ob_implicit_flush(true);
ob_end_flush();

use App\Services\PostService;
use App\Services\UserService;
use App\Controllers\PostController;
use App\Controllers\UserController;

require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/BaseController.php';
require_once __DIR__ . '/../src/Services/PostService.php';
require_once __DIR__ . '/../src/Services/UserService.php';
require_once __DIR__ . '/../src/Controllers/PostController.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$postService = new PostService();
$userService = new UserService();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$publicPages = ['/login', '/register', '/api/login', '/api/register'];
if (!in_array($path, $publicPages)) {
    if (empty($_SESSION['is_logged']) || $_SESSION['is_logged'] !== true) {
        if ($method === 'POST') {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'unauthorized',
                'message' => 'Требуется авторизация'
            ]);
            exit;
        }
        header('Location: /login');
        exit; 
    }
}
if (in_array($path, $publicPages)) {
    if (!empty($_SESSION['is_logged']) && $_SESSION['is_logged'] === true) {
        header('Location: /home');
        exit; 
    }
}

// Маршрутизация
switch ($path) {
    case '/':
    case '/home':
        $controller = new PostController($postService, $userService);
        $controller->renderHome();
        break;
    
    case '/login':
        $controller = new UserController($postService, $userService);
        $controller->renderLogin();
        break;

    case '/register':
        $controller = new UserController($postService, $userService);
        $controller->renderRegister();
        break;

    case '/profile':
        $controller = new UserController($postService, $userService);
        $controller->renderProfile();
        break;

    case '/edit/profile':
        $controller = new UserController($postService, $userService);
        $controller->renderEditProfile();
        break;

    case '/create':
        $controller = new PostController($postService, $userService);
        $controller->renderCreate();
        break;

    case '/edit':
        $controller = new PostController($postService, $userService);
        $controller->renderEdit();
        break;

    case '/api/login':
        $controller = new UserController($postService, $userService);
        $controller->login();
        break;

    case '/api/register':
        $controller = new UserController($postService, $userService);
        $controller->register();
        break;

    case '/logout':
        $controller = new UserController($postService, $userService);
        $controller->logout();
        break;

    case '/api/edit/profile':
        $controller = new UserController($postService, $userService);
        $controller->editProfile();
        break;

    case '/api/create':
        $controller = new PostController($postService, $userService);
        $controller->create();
        break;
    
    case '/api/edit':
        $controller = new PostController($postService, $userService);
        $id = $_GET['postId'];
        $controller->edit($id);
        break;
    
    case '/api/posts':
        $controller = new PostController($postService, $userService);
        $controller->getPostList();
        break;

    case '/api/post':
        $controller = new PostController($postService, $userService);
        $id = $_GET['id'];
        $controller->getPost($id);
        break;
    
    case '/api/users':
        $controller = new UserController($postService, $userService);
        $controller->getUserList();
        break;

    case '/api/user':
        $controller = new UserController($postService, $userService);
        $id = $_GET['id'];
        $controller->getUserById($id);
        break;

    case '/api/signed/user':
        $controller = new UserController($postService, $userService);
        $controller->getUserById($_SESSION['user_id']);
        break;

    case '/api/post/like':
        $postId = $_GET['postId'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new PostController($postService, $userService);
            $controller->toggleLike($postId);
        }
        break;

    case '/api/post/like/status':
        $postId = $_GET['postId'];
        $controller = new PostController($postService, $userService);
        $controller->isLiked($postId);
        break;
    
    default:
        http_response_code(404);
        echo 'Страница не найдена';
        break;
}