<?php

/**
 * Common database functions used across the application
 */

function safe_mysql_query($query) {
    return DatabaseUtils::getInstance()->query($query);
}

function mysql_real_escape_string($string) {
    return DatabaseUtils::getInstance()->escapeString($string);
}

function mysql_fetch_object($result) {
    return DatabaseUtils::getInstance()->fetchObject($result);
}

function mysql_fetch_array($result) {
    return DatabaseUtils::getInstance()->fetchArray($result);
}

function mysql_fetch_row($result) {
    return DatabaseUtils::getInstance()->fetchRow($result);
}

function mysql_num_rows($result) {
    return DatabaseUtils::getInstance()->numRows($result);
}

function mysql_affected_rows() {
    return DatabaseUtils::getInstance()->affectedRows();
}

function mysql_insert_id() {
    return DatabaseUtils::getInstance()->lastInsertId();
}

function create_in_clause($values, $isNumeric = false) {
    return DatabaseUtils::getInstance()->createInClause($values, $isNumeric);
}

?>