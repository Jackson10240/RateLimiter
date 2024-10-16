<?php
namespace Bytedance\RateLimiter\Storage;

use Bytedance\RateLimiter\DatabaseConnection;

class SqliteStorage implements StorageInterface
{
    private $db;

    /**
     * 构造函数，初始化SQLite数据库连接
     *
     * @param string $filePath SQLite数据库文件路径
     */
    public function __construct(string $filePath)
    {
        try {
            $dsn = "sqlite:$filePath";
            $this->db = new DatabaseConnection($dsn);
            $this->createTableIfNotExists();
        } catch (\PDOException $e) {
            throw new \RuntimeException("SQLite数据库连接失败: " . $e->getMessage());
        }
    }

    /**
     * 创建存储限流数据的表（如果不存在）
     */
    private function createTableIfNotExists(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS rate_limiter (
            key TEXT PRIMARY KEY,
            value TEXT NOT NULL
        )";
        $this->db->execute($query);
    }

    /**
     * 获取限流键的值
     *
     * @param string $key 限流键
     */
    public function get(string $key)
    {
        $query = "SELECT value FROM rate_limiter WHERE key = :key";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':key' => $key]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result['value'] : null;
    }

    /**
     * 设置限流键的值
     *
     * @param string $key 限流键
     * @param mixed $value 限流值
     */
    public function set(string $key, $value): void
    {
        $query = "INSERT OR REPLACE INTO rate_limiter (key, value) VALUES (:key, :value)";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':key' => $key, ':value' => $value]);
    }
}
