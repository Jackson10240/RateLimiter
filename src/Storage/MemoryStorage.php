<?php

namespace Bytedance\RateLimiter\Storage;

class MemoryStorage implements StorageInterface {

    private array $storage = [];

    public function __construct()
    {
        
    }

    public function get(string $key) {
        return $this->storage[$key] ?? null;
    }

    public function set(string $key, $value) {
        $this->storage[$key] = $value;
    }
}