<?php

require '../vendor/autoload.php';

use Bytedance\RateLimiter\RateLimiter;
use Bytedance\RateLimiter\Storage\MemoryStorage;
use Bytedance\RateLimiter\Strategies\TokenBucketStrategy;
use Bytedance\RateLimiter\Strategies\LeakyBucketStrategy;


// 使用MemoryStorage存储
$storage = new MemoryStorage();

// 使用TokenBucket策略
// $strategy = new TokenBucketStrategy();
// 使用LeakyBucketStrategy策略
$strategy = new LeakyBucketStrategy();


// 限流
$rateLimiter = new RateLimiter($strategy, $storage);

// 测试限流器
for ($i = 0; $i < 15; $i++) {
    if ($rateLimiter->limit('user:123', 1, 5)) {
        echo "Request $i allowed\n";
    } else {
        echo "Request $i denied\n";
    }
    sleep(1); // 模拟每秒一个请求
}