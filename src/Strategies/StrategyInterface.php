<?php

namespace Bytedance\RateLimiter\Strategies;

use Bytedance\RateLimiter\Storage\StorageInterface;

interface StrategyInterface
{
    public function limit(string $key, int $limit, int $interval, StorageInterface $storage): bool;
}
