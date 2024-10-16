<?php
namespace Bytedance\RateLimiter;

use Bytedance\RateLimiter\ConfigRateLimiter;
use Bytedance\RateLimiter\Storage\MemoryStorage;
use Bytedance\RateLimiter\Storage\RedisStorage;
use Bytedance\RateLimiter\Strategies\LeakyBucketStrategy;
use Bytedance\RateLimiter\Strategies\TokenBucketStrategy;

class RateLimiterFactory
{
    private $config;

    public function __construct($configFilePath)
    {
        $this->config = json_decode(file_get_contents($configFilePath), true);
    }

    public function createRateLimiter($name)
    {
        foreach ($this->config['rate_limiters'] as $limiterConfig) {
            if ($limiterConfig['name'] === $name) {
                $storage = $this->createStorage($limiterConfig['storage']);
                $strategy = $this->createStrategy($limiterConfig['strategy']);
                return new ConfigRateLimiter($storage, $strategy, $limiterConfig['key_pattern']);
            }
        }
        throw new \InvalidArgumentException("Rate limiter with name '$name' not found in configuration.");
    }

    private function createStorage($storageType)
    {
        switch ($storageType) {
            case 'Memory':
                return new MemoryStorage();
            case 'Redis':
                $redis = new \Redis();
                $redis->connect('127.0.0.1', 6379);
                return new RedisStorage($redis);
            // 添加更多的存储类型
            default:
                throw new \InvalidArgumentException("Unsupported storage type: $storageType");
        }
    }

    private function createStrategy($strategyType)
    {
        switch ($strategyType) {
            case 'LeakyBucket':
                return new LeakyBucketStrategy();
            case 'TokenBucket':
                return new TokenBucketStrategy();
            // 添加更多的策略类型
            default:
                throw new \InvalidArgumentException("Unsupported strategy type: $strategyType");
        }
    }

    public function getLimit($name)
    {
        foreach ($this->config['rate_limiters'] as $limiterConfig) {
            if ($limiterConfig['name'] === $name) {
                return $limiterConfig['limit'];
            }
        }
        throw new \InvalidArgumentException("Rate limiter with name '$name' not found in configuration.");
    }

    public function getInterval($name)
    {
        foreach ($this->config['rate_limiters'] as $limiterConfig) {
            if ($limiterConfig['name'] === $name) {
                return $limiterConfig['interval'];
            }
        }
        throw new \InvalidArgumentException("Rate limiter with name '$name' not found in configuration.");
    }
}
