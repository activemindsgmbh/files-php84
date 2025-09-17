<?php
declare(strict_types=1);

/**
 * Time and date utility functions
 */

/**
 * Get UNIX timestamp for a date
 * @param string $date Date string
 * @return int UNIX timestamp
 */
function date2time(string $date): int
{
    return (int)strtotime($date);
}

/**
 * Format UNIX timestamp as date
 * @param int $time UNIX timestamp
 * @return string Formatted date
 */
function time2date(int $time): string
{
    return date('Y-m-d', $time);
}

/**
 * Format UNIX timestamp as date and time
 * @param int $time UNIX timestamp
 * @return string Formatted date and time
 */
function time2datetime(int $time): string
{
    return date('Y-m-d H:i:s', $time);
}

/**
 * Get current timestamp
 * @return int Current UNIX timestamp
 */
function now(): int
{
    return time();
}

/**
 * Add days to a date
 * @param string $date Start date
 * @param int $days Days to add
 * @return string Resulting date
 */
function add_days(string $date, int $days): string
{
    $time = date2time($date);
    return time2date($time + ($days * 86400));
}

/**
 * Get days between two dates
 * @param string $date1 First date
 * @param string $date2 Second date
 * @return int Number of days
 */
function days_between(string $date1, string $date2): int
{
    $time1 = date2time($date1);
    $time2 = date2time($date2);
    return (int)(($time2 - $time1) / 86400);
}