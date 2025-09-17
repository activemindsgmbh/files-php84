<?php
/**
 * MySQL Database Connection Functions
 * Updated for PHP 8.4 compatibility
 */

require_once('database_functions.php');

function mysql_query_object($query)
{
    $res = safe_mysql_query($query);
    return mysql_fetch_object($res);
}

// Note: safe_mysql_query is now provided by database_functions.php
?>
