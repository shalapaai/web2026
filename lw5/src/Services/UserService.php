<?php
namespace App\Services;

class UserService {

    public function __construct(
        private string $dataPath,
    ) {}

    public function getAll(): array {
        return JsonStorageService::getData($this->dataPath);;
    }
}
?>