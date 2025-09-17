<?php
declare(strict_types=1);

/**
 * Error handling functions for PHP 8.4
 */

/**
 * Handle and log errors
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool
 */
function error_handler(int $errno, string $errstr, string $errfile, int $errline): bool
{
    switch ($errno) {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error_type = 'Fatal Error';
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            $error_type = 'Warning';
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error_type = 'Notice';
            break;
        default:
            $error_type = 'Unknown';
            break;
    }

    error_log("$error_type: $errstr in $errfile on line $errline");
    return true;
}

// Set error handler
set_error_handler('error_handler');

// Set error reporting based on environment
if ($GLOBALS['conf_environment'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', '0');
}