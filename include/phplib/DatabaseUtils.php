<?php

/**
 * DatabaseUtils class provides utility methods for database operations
 * and helps with the transition from mysql_* functions to mysqli
 */
class DatabaseUtils {
    private static $instance = null;
    private $db;

    private function __construct() {
        $this->db = Database::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function escapeString($string) {
        return $this->db->escapeString($string);
    }

    public function query($query) {
        return $this->db->query($query);
    }

    public function fetchObject($result) {
        return $result->fetch_object();
    }

    public function fetchAssoc($result) {
        return $result->fetch_assoc();
    }

    public function fetchArray($result) {
        return $result->fetch_array(MYSQLI_BOTH);
    }

    public function fetchRow($result) {
        return $result->fetch_row();
    }

    public function numRows($result) {
        return $result->num_rows;
    }

    public function affectedRows() {
        return $this->db->affectedRows();
    }

    public function lastInsertId() {
        return $this->db->lastInsertId();
    }

    /**
     * Safe query execution with proper escaping
     */
    public function safeQuery($query, $params = []) {
        foreach ($params as $key => $value) {
            if (is_int($value)) {
                $params[$key] = (int)$value;
            } elseif (is_float($value)) {
                $params[$key] = (float)$value;
            } else {
                $params[$key] = $this->escapeString($value);
            }
        }
        return $this->query($query);
    }

    /**
     * Generate WHERE IN clause safely
     */
    public function createInClause($values, $isNumeric = false) {
        if (empty($values)) {
            return 'NULL';
        }
        
        if ($isNumeric) {
            $values = array_map('intval', $values);
            return implode(',', $values);
        }
        
        $escaped = array_map([$this, 'escapeString'], $values);
        return "'" . implode("','", $escaped) . "'";
    }
}

?>