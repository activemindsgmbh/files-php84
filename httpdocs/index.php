<?php
declare(strict_types=1);

// Set up include path to find our include files
set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/../include');

// Load core system files
require_once('config.inc.php');
require_once('system.inc.php');

// Initialize system with new database connection
system_init();

// Load template
require_once("templates/html/index.html");
?>