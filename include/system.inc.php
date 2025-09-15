<?php
declare(strict_types=1);

require_once(__DIR__ . '/config.inc.php');
require_once(__DIR__ . '/Database.php');
require_once(__DIR__ . '/DatabaseUtils.php');
require_once(__DIR__ . '/StringUtils.php');
require_once(__DIR__ . '/Constants.php');

/**
 * Initialize the system by setting up database connection and other core components
 */
function system_init(): void
{
    // Initialize database connection using environment-specific configuration
    $db = Database::getInstance(
        $GLOBALS['conf_mysql_host'],
        $GLOBALS['conf_mysql_user'],
        $GLOBALS['conf_mysql_pass'],
        $GLOBALS['conf_mysql_db']
    );

    // Set error reporting based on environment
    if ($GLOBALS['conf_environment'] === 'development') {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    } else {
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        ini_set('display_errors', '0');
    }

    // Set timezone
    date_default_timezone_set('Europe/Berlin');
}