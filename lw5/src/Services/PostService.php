<?php
namespace App\Services;
use App\Models\Post;
use PDO;
use App\Core\Database;

class PostService {

    private PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAllPostList(): array {
        $query = <<<SQL
            SELECT 
                post.id,
                post.authorId,
                post.content,
                post.likes,
                post.createdAt,
                GROUP_CONCAT(image.path ORDER BY image.path SEPARATOR ',') AS images
            FROM post
            LEFT JOIN image ON post.id = image.postId
            GROUP BY post.id
            ORDER BY post.createdAt DESC
            SQL;
        $stmt = $this->pdo->query($query);
        
        $data = $stmt->fetchAll();
        foreach ($data as &$post) {
            $post = $this->convertImagesToArray($post);
        }
        // print_r($data);
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    public function getPostById(string $id): ?Post {
        $query = <<<SQL
            SELECT 
                post.id,
                post.authorId,
                post.content,
                post.likes,
                post.createdAt,
                GROUP_CONCAT(image.path ORDER BY image.path SEPARATOR ',') AS images
            FROM post
            LEFT JOIN image ON post.id = image.postId
            WHERE id = ?
            GROUP BY post.id
            SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        $data = $this->convertImagesToArray($data);
        return $data ? Post::fromArray($data) : null;
    }
    
    public function getPostsByAuthorId(string $authorId): array {
        $query = <<<SQL
            SELECT 
                post.id,
                post.authorId,
                post.content,
                post.likes,
                post.createdAt,
                GROUP_CONCAT(image.path ORDER BY image.path SEPARATOR ',') AS images
            FROM post
            LEFT JOIN image ON post.id = image.postId
            WHERE authorId = ?
            GROUP BY post.id
            ORDER BY post.createdAt DESC
            SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$authorId]);
        $data = $stmt->fetchAll();
        foreach ($data as &$post) {
            $post = $this->convertImagesToArray($post);
        }
        return array_map(fn($p) => Post::fromArray($p), $data);
    }

    public function createPost(array $data, string $authorId): void {
        $id = $this->generateUuid();
        echo $id;  
        $data['id'] = $id; 
        $data['authorId'] = $authorId;

        $this->saveToPostTable($data);

        $images = $data['uploadedImages'];
        foreach ($images as $image) {
            $this->saveToImageTable($id, $image);
        }
    }

    private function generateUuid(): string {
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

    private function saveToPostTable(array $data): void {
        $timestamp = time();
        $query = <<<SQL
            INSERT INTO post (id, authorId, content, createdAt)
            VALUES (?, ?, ?, $timestamp)
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$data['id'], $data['authorId'], $data['content']]);
        return;
    }

    private function saveToImageTable(string $id, string $imagePath): void {
        $query = <<<SQL
            INSERT INTO image (postId, path)
            VALUES (?, ?)
            SQL;
        $statement = $this->pdo->prepare($query);
        $statement->execute([$id, $imagePath]);
        return;
    }

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