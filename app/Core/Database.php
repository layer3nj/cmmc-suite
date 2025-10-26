<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database Connection and Query Builder
 */
class Database
{
    private PDO $pdo;
    private string $driver;
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect(): void
    {
        $dbConfig = $this->config->get('database');

        if (!$dbConfig) {
            throw new \Exception('Database configuration not found');
        }

        $this->driver = $dbConfig['driver'];
        $host = $dbConfig['host'];
        $port = $dbConfig['port'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];
        $charset = $dbConfig['charset'] ?? 'utf8mb4';

        try {
            if ($this->driver === 'mysql') {
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=$charset";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES $charset COLLATE utf8mb4_unicode_ci"
                ];
            } elseif ($this->driver === 'pgsql') {
                $dsn = "pgsql:host=$host;port=$port;dbname=$database";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
            } else {
                throw new \Exception("Unsupported database driver: $this->driver");
            }

            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database query error: ' . $e->getMessage() . "\nSQL: $sql");
            throw new \Exception('Database query failed: ' . $e->getMessage());
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    public function fetchColumn(string $sql, array $params = []): mixed
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function insert(string $table, array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ":$col", $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $params = [];
        foreach ($data as $key => $value) {
            $params[":$key"] = $value;
        }

        $this->query($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = [];
        $params = [];

        foreach ($data as $key => $value) {
            $sets[] = "$key = :set_$key";
            $params[":set_$key"] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            $where
        );

        $params = array_merge($params, $whereParams);
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $whereParams = []): int
    {
        $sql = "DELETE FROM $table WHERE $where";
        $stmt = $this->query($sql, $whereParams);
        return $stmt->rowCount();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    public function tableExists(string $table): bool
    {
        try {
            if ($this->driver === 'mysql') {
                $result = $this->query("SHOW TABLES LIKE ?", [$table]);
            } else { // pgsql
                $result = $this->query(
                    "SELECT EXISTS (SELECT FROM information_schema.tables WHERE table_name = ?)",
                    [$table]
                );
            }
            return $result->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function testConnection(string $driver, string $host, string $port, string $database, string $username, string $password): bool
    {
        try {
            if ($driver === 'mysql') {
                $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            } elseif ($driver === 'pgsql') {
                $dsn = "pgsql:host=$host;port=$port;dbname=$database";
            } else {
                return false;
            }

            $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            return true;
        } catch (PDOException $e) {
            error_log('Database connection test failed: ' . $e->getMessage());
            return false;
        }
    }
}
