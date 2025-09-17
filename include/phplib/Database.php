<?php

class Database {
    private static $instance = null;
    private $mysqli;

    private function __construct($host, $user, $pass, $db) {
        $this->mysqli = new mysqli($host, $user, $pass, $db);
        if ($this->mysqli->connect_error) {
            fatal_error('ERROR_DATABASE', $this->mysqli->connect_error, 'mysqli_connect');
        }
        $this->mysqli->set_charset('utf8');
    }

    public static function getInstance($host = null, $user = null, $pass = null, $db = null) {
        if (self::$instance === null) {
            self::$instance = new self($host, $user, $pass, $db);
        }
        return self::$instance;
    }

    public function query($query) {
        $result = $this->mysqli->query($query);
        if ($result === false) {
            fatal_error('ERROR_DATABASE', $this->mysqli->error, 'mysqli_query');
        }
        return $result;
    }

    public function escapeString($string) {
        return $this->mysqli->real_escape_string($string);
    }

    public function fetchArray($result) {
        return $result->fetch_array(MYSQLI_BOTH);
    }

    public function fetchAssoc($result) {
        return $result->fetch_assoc();
    }

    public function fetchRow($result) {
        return $result->fetch_row();
    }

    public function numRows($result) {
        return $result->num_rows;
    }

    public function affectedRows() {
        return $this->mysqli->affected_rows;
    }

    public function lastInsertId() {
        return $this->mysqli->insert_id;
    }

    public function close() {
        if ($this->mysqli) {
            $this->mysqli->close();
        }
    }
}
?>