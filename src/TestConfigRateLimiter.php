<?php
require '../vendor/autoload.php';

use Bytedance\RateLimiter\RateLimiterFactory;


// 使用示例
$factory = new RateLimiterFactory('config.json');
// $rateLimiter = $factory->createRateLimiter('user_limiter');
// $limit = $factory->getLimit('user_limiter');
// $interval = $factory->getInterval('user_limiter');


// 动态切换
$rateLimiter = $factory->createRateLimiter('global_limiter');
$limit = $factory->getLimit('global_limiter');
$interval = $factory->getInterval('global_limiter');


// 自定义限流参数
$context = ['user_id' => 123];

// 测试限流器
for ($i = 0; $i < 15; $i++) {
    if ($rateLimiter->limit($context, $limit, $interval)) {
        echo "Request $i allowed\n";
    } else {
        echo "Request $i denied\n";
    }
    sleep(1); // 模拟每秒一个请求
}