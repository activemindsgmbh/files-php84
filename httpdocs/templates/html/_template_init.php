<?php
declare(strict_types=1);

/**
 * Common template functions and variable initialization
 */

// Initialize commonly used variables with null coalescing operator
$kunde = $kunde ?? null;
$owner = $owner ?? null;
$rkunde = $rkunde ?? null;
$rdomains = $rdomains ?? null;
$domain = $domain ?? null;
$result = $result ?? null;

/**
 * Safe echo with HTML escaping
 * @param mixed $value Value to echo
 * @return void
 */
function safe_echo(mixed $value): void {
    echo htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Safe URL encode
 * @param mixed $value Value to encode
 * @return string
 */
function safe_url(mixed $value): string {
    return urlencode((string)$value);
}

/**
 * Check if a variable is a valid database result
 * @param mixed $result Result to check
 * @return bool
 */
function is_valid_result(mixed $result): bool {
    return $result instanceof mysqli_result && $result->num_rows > 0;
}

/**
 * Get a property from an object safely
 * @param mixed $obj Object to get property from
 * @param string $prop Property name
 * @param mixed $default Default value
 * @return mixed
 */
function get_prop(mixed $obj, string $prop, mixed $default = ''): mixed {
    if (!is_object($obj)) {
        return $default;
    }
    return $obj->$prop ?? $default;
}