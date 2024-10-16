<?php
namespace Bytedance\RateLimiter;

use Bytedance\RateLimiter\Strategies\StrategyInterface;
use Bytedance\RateLimiter\Storage\StorageInterface;

class RateLimiter implements RateLimiterInterface
{
    private $strategy;
    private $storage;

    public function __construct(StrategyInterface $strategy, StorageInterface $storage)
    {
        $this->strategy = $strategy;
        $this->storage = $storage;
    }

    public function limit(string $key, int $limit, int $interval): bool
    {
        return $this->strategy->limit($key, $limit, $interval, $this->storage);
    }
}
