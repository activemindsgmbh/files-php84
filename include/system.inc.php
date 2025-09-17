<?php
declare(strict_types=1);

// Use __DIR__ for absolute paths to ensure files are found regardless of the calling context
require_once(__DIR__ . '/config.inc.php');
require_once(__DIR__ . '/phplib/error.inc.php');
require_once(__DIR__ . '/phplib/Database.php');
require_once(__DIR__ . '/phplib/DatabaseUtils.php');
require_once(__DIR__ . '/phplib/StringUtils.php');
require_once(__DIR__ . '/phplib/Constants.php');
require_once(__DIR__ . '/phplib/database_functions.php');
require_once(__DIR__ . '/phplib/session.inc.php');
require_once(__DIR__ . '/phplib/stdlib.inc.php');
require_once(__DIR__ . '/phplib/time.inc.php');

/**
 * Initialize the system
 * Sets up database connection and error handling
 */
function system_init(): void
{
    // Get database configuration
    $db = Database::getInstance(
        $GLOBALS['conf_mysql_host'],
        $GLOBALS['conf_mysql_user'],
        $GLOBALS['conf_mysql_pass'],
        $GLOBALS['conf_mysql_db']
    );

    // Set error handling based on environment
    if ($GLOBALS['conf_environment'] === 'development') {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    } else {
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        ini_set('display_errors', '0');
    }

    // Set default timezone
    date_default_timezone_set('Europe/Berlin');

    // Initialize session
    Session::getInstance();
}
