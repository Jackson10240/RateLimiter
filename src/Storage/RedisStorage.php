<?php
namespace Bytedance\RateLimiter\Storage;

class RedisStorage implements StorageInterface
{
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function set(string $key, $value)
    {
        $this->redis->set($key, $value);
    }
}
