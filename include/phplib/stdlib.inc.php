<?php
declare(strict_types=1);

/**
 * Standard library functions
 */

/**
 * Format a number as currency
 * @param float|int $number Number to format
 * @param string $currency Currency symbol
 * @return string Formatted currency string
 */
function format_currency(float|int $number, string $currency = '€'): string
{
    return number_format($number, 2, ',', '.') . ' ' . $currency;
}

/**
 * Check if value is unsigned integer
 * @param mixed $value Value to check
 * @return bool True if unsigned int
 */
function isuint(mixed $value): bool
{
    return is_numeric($value) && $value == (int)$value && $value >= 0;
}

/**
 * Get POST value with optional default
 * @param string $key POST key
 * @param mixed $default Default value
 * @return mixed POST value or default
 */
function post_value(string $key, mixed $default = ''): mixed
{
    return $_POST[$key] ?? $default;
}

/**
 * Get GET value with optional default
 * @param string $key GET key
 * @param mixed $default Default value
 * @return mixed GET value or default
 */
function get_value(string $key, mixed $default = ''): mixed
{
    return $_GET[$key] ?? $default;
}

/**
 * MIME encode string
 * @param string $str String to encode
 * @return string Encoded string
 */
function mime_encode(string $str): string
{
    return mb_encode_mimeheader($str, 'UTF-8', 'B');
}

/**
 * Format date in German format
 * @param string|int $date Date string or timestamp
 * @return string Formatted date
 */
function format_date(string|int $date): string
{
    if (is_string($date)) {
        $timestamp = strtotime($date);
    } else {
        $timestamp = $date;
    }
    return date('d.m.Y', $timestamp);
}