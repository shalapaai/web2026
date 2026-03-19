<?php
namespace App\Services;

class JsonStorageService {
    
    public function __construct(
        protected string $dataPath
    ) {}
    
    public static function getData(string $path): array {
        if (!file_exists($path)) {
            return [];  
        }
        $json = file_get_contents($path);
        if ($json === false) {
            throw new \Exception("Не удалось прочитать файл: {$path}");
        }
        if (trim($json) === '') {
            return [];
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                "Ошибка JSON в {$path}: " . json_last_error_msg()
            );
        }
        if (!is_array($data)) {
            throw new \Exception(
                "Ожидался массив в {$path}, получено: " . gettype($data)
            );
        }
        
        return $data;
    }  
}