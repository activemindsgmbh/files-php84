<?php
declare(strict_types=1);

class DatabaseUtils {
    private static ?DatabaseUtils $instance = null;
    private Database $db;

    private function __construct() {
        $this->db = Database::getInstance();
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createInClause(array $values, bool $isNumeric = false): string {
        if (empty($values)) {
            return 'NULL';
        }
        
        if ($isNumeric) {
            $values = array_map('intval', $values);
            return implode(',', $values);
        }
        
        $escaped = array_map([$this->db, 'escapeString'], $values);
        return "'" . implode("','", $escaped) . "'";
    }

    public function safeQuery(string $query, array $params = []): mysqli_result|bool {
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $params[$key] = (int)$value;
            } elseif (is_float($value)) {
                $params[$key] = (float)$value;
            } else {
                $params[$key] = $this->db->escapeString((string)$value);
            }
        }
        return $this->db->query($query);
    }
}
