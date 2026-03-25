<?php
namespace App\Services;

abstract class JsonStorageService {

    function __construct(
        private string $dataPath
    ) {}
    
    protected function readJson(): array {
        if (!file_exists($this->dataPath)) {
            return [];
        }
        $json = file_get_contents($this->dataPath);
        if ($json === false) {
            throw new \Exception("Не удалось прочитать файл: {$this->dataPath}");
        }
        if (trim($json) === '') {
            return [];
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                "Ошибка JSON в {$this->dataPath}: " . json_last_error_msg()
            );
        }
        
        return $data;
    }

    protected function writeJson(array $data): void {
        // print_r($data);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        if ($json === false) {
            throw new \Exception("Не удалось закодировать JSON");
        }

        // print_r($json);
        
        if (file_put_contents($this->dataPath, $json) === false) {
            throw new \Exception("Не удалось записать файл: {$this->dataPath}");
        }
    }
}