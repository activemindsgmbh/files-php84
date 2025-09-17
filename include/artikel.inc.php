<?php
declare(strict_types=1);

require_once(__DIR__ . '/phplib/mysql.inc.php');
require_once(__DIR__ . '/phplib/stdlib.inc.php');

/**
 * Article (Artikel) related functions
 */

/**
 * Read article data by ID
 * @param int $id Article ID
 * @return ?object Article data or null
 */
function read_artikel(int $id): ?object
{
    $result = db_query('SELECT * FROM artikel WHERE artikelnummer = ' . $id);
    if (!$result || !($result instanceof mysqli_result)) {
        return null;
    }
    return $result->fetch_object() ?: null;
}

/**
 * Update article data
 * @param int $id Article ID
 * @param array $data Article data
 * @return bool Success status
 */
function update_artikel(int $id, array $data): bool
{
    $sets = [];
    foreach ($data as $key => $value) {
        $sets[] = $key . ' = ' . db_escape($value);
    }
    
    if (empty($sets)) {
        return false;
    }

    $query = 'UPDATE artikel SET ' . implode(', ', $sets) . 
             ' WHERE artikelnummer = ' . $id;
    
    return (bool)db_query($query);
}

/**
 * Create new article
 * @param array $data Article data
 * @return int|false New article ID or false on failure
 */
function create_artikel(array $data): int|false
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

    $query = 'INSERT INTO artikel (' . implode(', ', $fields) . 
             ') VALUES (' . implode(', ', $values) . ')';
    
    if (!db_query($query)) {
        return false;
    }

    return db_insert_id();
}

/**
 * Delete article
 * @param int $id Article ID
 * @return bool Success status
 */
function delete_artikel(int $id): bool
{
    return (bool)db_query('DELETE FROM artikel WHERE artikelnummer = ' . $id);
}

/**
 * Search articles
 * @param string $pattern Search pattern
 * @return mysqli_result|false Search results
 */
function search_artikel(string $pattern): mysqli_result|false
{
    $pattern = db_escape($pattern);
    return db_query(
        "SELECT * FROM artikel WHERE 
         bezeichnung LIKE '%$pattern%' OR 
         beschreibung LIKE '%$pattern%' 
         ORDER BY bezeichnung"
    );
}