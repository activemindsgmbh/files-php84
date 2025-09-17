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
    $db = Database::getInstance(
        $GLOBALS['conf_mysql_host'],
        $GLOBALS['conf_mysql_user'],
        $GLOBALS['conf_mysql_pass'],
        $GLOBALS['conf_mysql_db']
    );
}

?>