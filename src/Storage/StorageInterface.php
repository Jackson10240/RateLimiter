<?php
namespace Bytedance\RateLimiter\Storage;


interface StorageInterface
{
    public function get(string $key);
    public function set(string $key, $value);
}
