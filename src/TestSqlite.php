
<?php
require '../vendor/autoload.php';

use Bytedance\RateLimiter\RateLimiter;
use Bytedance\RateLimiter\Storage\SqliteStorage;
use Bytedance\RateLimiter\Strategies\TokenBucketStrategy;
use Bytedance\RateLimiter\Strategies\LeakyBucketStrategy;

// 初始化SQLite存储
$storage = new SqliteStorage('/Users/bytedance/php/RateLimiter/database.sqlite');

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