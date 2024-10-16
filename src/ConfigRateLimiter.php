<?php
namespace Bytedance\RateLimiter;

class ConfigRateLimiter
{
    private $storage;
    private $strategy;
    private $keyPattern;

    public function __construct($storage, $strategy, $keyPattern)
    {
        $this->storage = $storage;
        $this->strategy = $strategy;
        $this->keyPattern = $keyPattern;
    }

    public function limit($context,int $limit, int $interval): bool
    {
        $key = $this->generateKey($context);
        return $this->strategy->limit($key, $limit, $interval, $this->storage);
    }   

    private function generateKey($context)
    {
        $replacements = [
            '{user_id}' => $context['user_id'] ?? '',
            // 可以添加更多的自定义字段
        ];
        return strtr($this->keyPattern, $replacements);
    }
}
