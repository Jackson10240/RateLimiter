# RateLimiter 项目

## 简介

`RateLimiter` 是一个用于限制请求频率的PHP库。它支持多种存储后端（如Redis和数据库，比如 Sqlite），并提供不同的限流策略（如令牌桶策略）。本项目旨在帮助开发者轻松实现请求限流功能。

并且实现动态配置，如果结合配置中心，可以实现实时调整限流策略。

## 项目结构

```
/Users/bytedance/php/RateLimiter/
    ├── composer.json
    ├── src/
    │   ├── Storage/
    │   │   ├── MemoryStorage.php
    │   │   ├── RedisStorage.php
    │   │   ├── SqliteStorage.php
    │   │   └── StorageInterface.php
    │   ├── Strategies/
    │   │   ├── LeakyBucketStrategy.php
    │   │   └── StrategyInterface.php
    │   │   ├── TokenBucketStrategy.php
    │   ├── config.json
    │   └── ConfigRateLimiter.php
    │   ├── DatabaseConnection.php
    │   ├── RateLimiter.php
    │   ├── RateLimiterInterface.php
    │   └── TestConfigRateLimiter.php
    │   └── TestPHPInfo.php
    │   └── TestSqlite.php
    │   └── TestRedis.php
    │   └── README.md
    └── vendor/
```

## 安装

### 使用 Composer 安装

```bash
composer require bytedance/rate-limiter
```

### 手动安装

1. 克隆项目到本地：

    ```bash
    git clone https://github.com/bytedance/rate-limiter.git
    ```

2. 进入项目目录：

    ```bash
    cd rate-limiter
    ```

3. 安装依赖：

    ```bash
    composer install
    ```



## 使用示例

### 使用 Redis 存储

```php
<?php

require '../vendor/autoload.php';

use Bytedance\RateLimiter\RateLimiter;
use Bytedance\RateLimiter\Storage\RedisStorage;
use Bytedance\RateLimiter\Strategies\TokenBucketStrategy;
use Bytedance\RateLimiter\Strategies\LeakyBucketStrategy;


// 使用Redis存
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);
$storage = new RedisStorage($redis);

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
```

### 使用Sqlite存储

```php
<?php
require '../vendor/autoload.php';

use Bytedance\RateLimiter\RateLimiter;
use Bytedance\RateLimiter\Storage\SqliteStorage;
use Bytedance\RateLimiter\Strategies\TokenBucketStrategy;
use Bytedance\RateLimiter\Strategies\LeakyBucketStrategy;

// 初始化SQLite存储
$storage = new SqliteStorage('/Users/bytedance/php/RateLimiter/database.sqlite');

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
```


## 数据库表结构


确保你的数据库中有一个名为`rate_limiter`的表，结构如下：

```sql
CREATE TABLE IF NOT EXISTS rate_limiter (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL
);
```


### 使用内存存储

```php
<?php

require '../vendor/autoload.php';

use Bytedance\RateLimiter\RateLimiter;
use Bytedance\RateLimiter\Storage\MemoryStorage;
use Bytedance\RateLimiter\Strategies\TokenBucketStrategy;
use Bytedance\RateLimiter\Strategies\LeakyBucketStrategy;


// 使用MemoryStorage存储
$storage = new MemoryStorage();

// 使用TokenBucket策略
$strategy = new TokenBucketStrategy();
// 使用LeakyBucketStrategy策略
// $strategy = new LeakyBucketStrategy();


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
```


## 动态配置
```json
{
  "rate_limiters": [
    {
      "name": "global_limiter",
      "strategy": "TokenBucket",
      "storage": "Redis",
      "limit": 1,
      "interval": 5,
      "key_pattern": "global_limiter"
    },
    {
      "name": "user_limiter",
      "strategy": "LeakyBucket",
      "storage": "Memory",
      "limit": 1,
      "interval": 5,
      "key_pattern": "user_limiter:{user_id}"
    }
  ]
}

```

```php
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
```


## 依赖

- PHP 7.4 或更高版本
- Redis（如果使用Redis存储）
- MySQL 或其他支持PDO的数据库（如果使用数据库存储）

## 贡献

欢迎贡献代码！请提交Pull Request，并确保遵循项目的编码规范。


## 许可证

本项目采用 MIT 许可证。详细信息请参阅 xxx 文件。
s

## 联系

如有任何问题或建议，请通过 xxx 联系我们。
```
