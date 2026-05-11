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

// 2. Список страниц, куда можно зайти БЕЗ авторизации
$publicPages = ['/login', '/register'];

// 3. Если страница НЕ публичная И пользователь НЕ авторизован → редирект
if (!in_array($path, $publicPages)) {
    if (empty($_SESSION['is_logged']) || $_SESSION['is_logged'] !== true) {
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
        $controller->home();
        break;
    
    case '/login':
        $controller = new UserController($postService, $userService);
        $controller->login();
        break;
    
    case '/register':
        $controller = new UserController($postService, $userService);
        $controller->register();
        break;

    case '/logout':
        $controller = new UserController($postService, $userService);
        $controller->logout();
        break;
    
    case '/profile':
        $controller = new UserController($postService, $userService);
        $controller->profile();
        break;
    
    case '/create':
        $controller = new PostController($postService, $userService);
        $controller->create();
        break;
    
    case '/edit':
        $controller = new PostController($postService, $userService);
        $controller->edit();
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
    
    default:
        http_response_code(404);
        echo 'Страница не найдена';
        break;
}