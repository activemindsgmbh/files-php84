<?php
declare(strict_types=1);

function safe_mysql_query(string $query): mysqli_result|bool {
    return DatabaseUtils::getInstance()->safeQuery($query);
}

function mysql_real_escape_string(string $string): string {
    return Database::getInstance()->escapeString($string);
}

function mysql_fetch_object(mysqli_result $result): ?object {
    return Database::getInstance()->fetchObject($result);
}

function mysql_num_rows(mysqli_result $result): int {
    return Database::getInstance()->numRows($result);
}

function mysql_affected_rows(): int {
    return Database::getInstance()->affectedRows();
}
