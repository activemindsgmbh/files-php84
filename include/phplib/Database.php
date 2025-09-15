<?php
declare(strict_types=1);

class Database {
    private static ?Database $instance = null;
    private mysqli $mysqli;

    private function __construct(string $host, string $user, string $pass, string $db) {
        $this->mysqli = new mysqli($host, $user, $pass, $db);
        if ($this->mysqli->connect_error) {
            throw new RuntimeException("Database connection failed: " . $this->mysqli->connect_error);
        }
        $this->mysqli->set_charset('utf8mb4');
    }

    public static function getInstance(?string $host = null, ?string $user = null, ?string $pass = null, ?string $db = null): self {
        if (self::$instance === null) {
            if ($host === null || $user === null || $pass === null || $db === null) {
                throw new RuntimeException("Database connection parameters required");
            }
            self::$instance = new self($host, $user, $pass, $db);
        }
        return self::$instance;
    }

    public function query(string $query): mysqli_result|bool {
        $result = $this->mysqli->query($query);
        if ($result === false) {
            throw new RuntimeException("Query failed: " . $this->mysqli->error);
        }
        return $result;
    }

    public function escapeString(string $string): string {
        return $this->mysqli->real_escape_string($string);
    }

    public function fetchObject(mysqli_result $result): ?object {
        return $result->fetch_object();
    }

    public function numRows(mysqli_result $result): int {
        return $result->num_rows;
    }

    public function affectedRows(): int {
        return $this->mysqli->affected_rows;
    }
}
