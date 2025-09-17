<?php
declare(strict_types=1);

require_once('error.inc.php');
require_once('Database.php');

/**
 * MySQL utility functions
 */

/**
 * Escape a string for use in a MySQL query
 * @param string $str String to escape
 * @return string Escaped string
 */
function db_escape(string $str): string 
{
    $db = Database::getInstance();
    return $db->escape($str);
}

/**
 * Execute a MySQL query
 * @param string $query Query to execute
 * @return mysqli_result|bool Query result
 */
function db_query(string $query): mysqli_result|bool
{
    $db = Database::getInstance();
    return $db->query($query);
}

/**
 * Get the last inserted ID
 * @return int Last insert ID
 */
function db_insert_id(): int
{
    $db = Database::getInstance();
    return $db->lastInsertId();
}

/**
 * Get number of affected rows
 * @return int Number of affected rows
 */
function db_affected_rows(): int
{
    $db = Database::getInstance();
    return $db->affectedRows();
}