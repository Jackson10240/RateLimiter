<?php
namespace Bytedance\RateLimiter;

class DatabaseConnection
{
    private $pdo;

    /**
     * 构造函数，初始化数据库连接
     *
     * @param string $dsn 数据库连接字符串
     * @param string $username 数据库用户名
     * @param string $password 数据库密码
     */
    public function __construct(string $dsn, string $username = '', string $password = '')
    {
        try {
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \RuntimeException("数据库连接失败: " . $e->getMessage());
        }
    }

    /**
     * 准备并返回一个PDOStatement对象
     *
     * @param string $query SQL查询语句
     * @return \PDOStatement
     */
    public function prepare(string $query): \PDOStatement
    {
        return $this->pdo->prepare($query);
    }

    /**
     * 执行一个SQL查询并返回结果集
     *
     * @param string $query SQL查询语句
     * @return array 结果集数组
     */
    public function query(string $query): array
    {
        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 执行一个SQL语句（如INSERT, UPDATE, DELETE）
     *
     * @param string $query SQL语句
     * @return int 受影响的行数
     */
    public function execute(string $query): int
    {
        return $this->pdo->exec($query);
    }
}
