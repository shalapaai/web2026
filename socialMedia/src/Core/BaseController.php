<?php

namespace App\Core;

abstract class BaseController {
    protected function getCurrentUser(): ?array {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            return ['id' => $_SESSION['user_id']];
        }
        return null;
    }

    protected function render(string $view, array $data = []): void {
        include __DIR__ . "/../Views/pages/{$view}.php";  
        switch ($view) {
            case 'create':
                renderCreatePage($data);
                break;
            case 'edit':
                renderEditPage($data);
                break;
            case 'home':
                renderHomePage($data);
                break;
            case 'login':
                renderLoginPage($data);
                break;
            case 'profile':
                renderProfilePage($data);
                break;
        }   
    }

    protected function generateUuid(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    protected function sendJson(bool $success, ?string $error = null, ?string $message = null, array $data = []): void {
        $response = ['success' => $success];
        if ($error) $response['error'] = $error;
        if ($message) $response['message'] = $message;
        if ($data) $response['data'] = $data;
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}