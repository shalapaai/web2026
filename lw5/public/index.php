<?php
namespace App;

use App\Services\PostService;
use App\Services\UserService;
use App\Controllers\PostController;
use App\Controllers\UserController;
require_once __DIR__ . '/../src/Services/PostService.php';
require_once __DIR__ . '/../src/Services/UserService.php';
require_once __DIR__ . '/../src/Services/JsonStorageService.php';
require_once __DIR__ . '/../src/Controllers/PostController.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Создаём сервисы
$postService = new PostService(__DIR__ . '/../data/posts.json');
$userService = new UserService(__DIR__ . '/../data/users.json');

// Маршрутизация
switch ($request) {
    case '/':
    case '/home':
        $controller = new PostController($postService, $userService);
        $controller->index();
        break;
    
    case '/login':
        $controller = new UserController($userService);
        $controller->login();
        break;
    
    case '/profile':
        $controller = new UserController($userService);
        $controller->profile();
        break;
    
    case '/create':
        if ($method === 'POST') {
            $controller = new PostController($postService, $userService);
            $controller->create();
        } else {
            include __DIR__ . '/../views/pages/create.php';
        }
        break;
    
    default:
        http_response_code(404);
        echo 'Страница не найдена';
        break;
}
?>