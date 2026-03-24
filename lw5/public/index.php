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
require_once __DIR__ . '/../src/Services/JsonStorageService.php';
require_once __DIR__ . '/../src/Services/PostService.php';
require_once __DIR__ . '/../src/Services/UserService.php';
require_once __DIR__ . '/../src/Core/BaseController.php';
require_once __DIR__ . '/../src/Controllers/PostController.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$postService = new PostService(__DIR__ . '/../data/posts.json');
$userService = new UserService(__DIR__ . '/../data/users.json');

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
    
    default:
        http_response_code(404);
        echo 'Страница не найдена';
        break;
}
?>