<?php
declare(strict_types=1);

require_once(__DIR__ . '/phplib/mysql.inc.php');
require_once(__DIR__ . '/phplib/stdlib.inc.php');

/**
 * Customer (Kunde) related functions
 */

/**
 * Read customer data by ID
 * @param int $id Customer ID
 * @return ?object Customer data or null
 */
function read_kunde(int $id): ?object
{
    $result = db_query('SELECT * FROM kunde WHERE kundennummer = ' . $id);
    if (!$result || !($result instanceof mysqli_result)) {
        return null;
    }
    return $result->fetch_object() ?: null;
}

/**
 * Update customer data
 * @param int $id Customer ID
 * @param array $data Customer data
 * @return bool Success status
 */
function update_kunde(int $id, array $data): bool
{
    $sets = [];
    foreach ($data as $key => $value) {
        $sets[] = $key . ' = ' . db_escape($value);
    }
    
    if (empty($sets)) {
        return false;
    }

    $query = 'UPDATE kunde SET ' . implode(', ', $sets) . 
             ' WHERE kundennummer = ' . $id;
    
    return (bool)db_query($query);
}

/**
 * Create new customer
 * @param array $data Customer data
 * @return int|false New customer ID or false on failure
 */
function create_kunde(array $data): int|false
{
    $fields = [];
    $values = [];
    
    foreach ($data as $key => $value) {
        $fields[] = $key;
        $values[] = db_escape($value);
    }
    
    if (empty($fields)) {
        return false;
    }

    $query = 'INSERT INTO kunde (' . implode(', ', $fields) . 
             ') VALUES (' . implode(', ', $values) . ')';
    
    if (!db_query($query)) {
        return false;
    }

    return db_insert_id();
}

/**
 * Delete customer
 * @param int $id Customer ID
 * @return bool Success status
 */
function delete_kunde(int $id): bool
{
    return (bool)db_query('DELETE FROM kunde WHERE kundennummer = ' . $id);
}

/**
 * Search customers
 * @param string $pattern Search pattern
 * @return mysqli_result|false Search results
 */
function search_kunden(string $pattern): mysqli_result|false
{
    $pattern = db_escape($pattern);
    return db_query(
        "SELECT * FROM kunde WHERE 
         name LIKE '%$pattern%' OR 
         vorname LIKE '%$pattern%' OR 
         firma LIKE '%$pattern%' 
         ORDER BY name, vorname"
    );
}