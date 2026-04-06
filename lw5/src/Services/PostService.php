<?php
namespace App\Services;
use App\Models\Post;
use PDO;
use App\Core\Database;

class PostService {

    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        // print_r($this->pdo);
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("
            SELECT 
                p.id,
                p.authorId,
                p.content,
                p.likes,
                p.createdAt,
                GROUP_CONCAT(i.path ORDER BY i.path SEPARATOR ',') AS images
            FROM post p
            LEFT JOIN image i ON p.id = i.postId
            GROUP BY p.id
        ");
        $data = $stmt->fetchAll();
        foreach ($data as &$post) {
            $post = $this->convertImagesToArray($post);
        }
        // print_r($data);
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    public function getById(string $id): ?Post {
        $stmt = $this->pdo->query("
            SELECT 
                p.id,
                p.authorId,
                p.content,
                p.likes,
                p.createdAt,
                GROUP_CONCAT(i.path ORDER BY i.path SEPARATOR ',') AS images
            FROM post p
            LEFT JOIN image i ON p.id = i.postId
            WHERE id = '$id'
            GROUP BY p.id
        ");
        $data = $stmt->fetch();
        $data = $this->convertImagesToArray($data);
        return $data ? Post::fromArray($data) : null;
    }
    
    public function getByAuthorId(string $authorId): array {
        $stmt = $this->pdo->query("
            SELECT 
                p.id,
                p.authorId,
                p.content,
                p.likes,
                p.createdAt,
                GROUP_CONCAT(i.path ORDER BY i.path SEPARATOR ',') AS images
            FROM post p
            LEFT JOIN image i ON p.id = i.postId
            WHERE authorId = '$authorId'
            GROUP BY p.id
        ");
        $data = $stmt->fetchAll();
        foreach ($data as &$post) {
            $post = $this->convertImagesToArray($post);
        }
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    // public function create($data, int $authorId): Post {
    //     $posts = $this->readJson();
    //     $id = end($posts)['id'] + 1;
    //     $newPost = [
    //         'id' => $id,
    //         'authorId' => $authorId,
    //         'content' => trim($data['content'] ?? ''),
    //         'images' => $data['uploadedImages'] ?? [],
    //         'likes' => 0,
    //         'createdAt' => time(),
    //     ];
        
    //     $posts[] = $newPost;
    //     print_r($newPost);
    //     $this->writeJson($posts);
        
    //     return Post::fromArray($newPost);
    // }

    public function uploadImages(?array $files): array {
        if (!$files || empty($files['name'][0])) {
            return [];
        }
        $uploaded = [];
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadDir = __DIR__ . '/../../public/uploads/posts/';
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                error_log("Ошибка загрузки файла #{$i}: " . $this->getUploadErrorMessage($files['error'][$i]));
                continue;
            }
            if (!in_array($files['type'][$i], $allowedTypes)) {
                throw new \Exception("Недопустимый формат файла #{$i}: {$files['type'][$i]}");
            }
            if ($files['size'][$i] > $maxSize) {
                throw new \Exception("Файл #{$i} слишком большой: " . round($files['size'][$i] / 1024 / 1024, 2) . "MB");
            }
            $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . time() . '_' . $i . '.' . $extension;
            $destination = $uploadDir . $fileName;
            if (!move_uploaded_file($files['tmp_name'][$i], $destination)) {
                throw new \Exception("Не удалось сохранить файл #{$i}");
            }
            $uploaded[] = '/' . $fileName;
        }
        
        return $uploaded;
    }

    private function getUploadErrorMessage(int $errorCode): string {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Превышен upload_max_filesize в php.ini',
            UPLOAD_ERR_FORM_SIZE => 'Превышен MAX_FILE_SIZE в форме',
            UPLOAD_ERR_PARTIAL => 'Файл загружен частично',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION => 'Загрузка прервана расширением PHP',
        ];
        return $messages[$errorCode] ?? 'Неизвестная ошибка';
    }

    private function convertImagesToArray(array $post): array {
        $post['images'] = empty($post['images']) 
            ? [] 
            : array_map('trim', explode(',', $post['images']));
        return $post;
    }
}