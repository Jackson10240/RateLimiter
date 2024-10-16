<?php
namespace Bytedance\RateLimiter;

interface RateLimiterInterface
{
    public function limit(string $key, int $limit, int $interval): bool;
}