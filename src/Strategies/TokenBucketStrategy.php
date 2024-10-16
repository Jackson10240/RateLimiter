<?php
namespace Bytedance\RateLimiter\Strategies;

use Bytedance\RateLimiter\Storage\StorageInterface;

/**
 * 令牌桶策略类，实现了StrategyInterface接口
 */
class TokenBucketStrategy implements StrategyInterface
{
    /**
     * 执行限流检查
     *
     * @param string $key 限流键
     * @param int $limit 每单位时间允许的请求数
     * @param int $interval 单位时间（秒）
     * @param StorageInterface $storage 存储接口实例
     * @return bool 如果请求被允许则返回true，否则返回false
     */
    public function limit(string $key, int $limit, int $interval, StorageInterface $storage): bool
    {
        // 获取当前时间
        $currentTime = time();

        // 获取上次填充令牌的时间，如果未设置则使用当前时间
        $lastRefillTime = $storage->get($key . ':last_refill_time') ?? $currentTime;

        // 获取当前令牌数，如果未设置则使用最大令牌数
        $tokens = $storage->get($key . ':tokens') ?? $limit;

        // 计算自上次填充令牌以来的时间间隔
        $timeSinceLastRefill = $currentTime - $lastRefillTime;

        // 计算应添加的令牌数
        $tokensToAdd = $timeSinceLastRefill * ($limit / $interval);

        // 更新令牌数，确保不超过最大令牌数
        $tokens = min($tokens + $tokensToAdd, $limit);

        // 如果令牌数大于等于1，则允许请求
        if ($tokens >= 1) {
            // 消耗一个令牌
            $tokens--;

            // 更新令牌数和上次填充令牌的时间
            $storage->set($key . ':tokens', $tokens);
            $storage->set($key . ':last_refill_time', $currentTime);

            // 返回请求被允许
            return true;
        }

        // 返回请求被拒绝
        return false;
    }
}
