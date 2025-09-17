<?php

require_once ('config.inc.php');
require_once ('phplib/error.inc.php');
require_once ('phplib/Database.php');
require_once ('phplib/DatabaseUtils.php');
require_once ('phplib/StringUtils.php');
require_once ('phplib/Constants.php');
require_once ('phplib/database_functions.php');
require_once ('phplib/session.inc.php');
require_once ('phplib/stdlib.inc.php');
require_once ('phplib/time.inc.php');

function system_init()
{
    // Initialize database connection
    $db = Database::getInstance(
        $GLOBALS['conf_mysql_host'],
        $GLOBALS['conf_mysql_user'],
        $GLOBALS['conf_mysql_pass'],
        $GLOBALS['conf_mysql_db']
    );

    // Initialize database utils
    DatabaseUtils::getInstance();

    // Start session
    page_open([
        'sessionType' => 'Session',
        'storeType'  => 'File',
        'domain'     => $GLOBALS['conf_domain'],
        'path'       => '/',
        'expire'     => $GLOBALS['conf_session_expire']
    ]);

    // Set error reporting
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 'Off');
    ini_set('log_errors', 'On');
}

?>
