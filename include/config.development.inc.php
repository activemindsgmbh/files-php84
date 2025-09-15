<?php
declare(strict_types=1);

/**
 * Development environment configuration
 * IMPORTANT: Never commit this file with real credentials
 */

// Development Database
$GLOBALS['conf_mysql_db']   = 'dev_database';
$GLOBALS['conf_mysql_host'] = 'localhost';
$GLOBALS['conf_mysql_user'] = 'dev_user';
$GLOBALS['conf_mysql_pass'] = 'dev_password';

// Development Paths
$GLOBALS['conf_logo'] = __DIR__ . '/../../data/logo.png';
$GLOBALS['conf_dir_mahnung'] = __DIR__ . '/../../httpdocs/pdf/mahnungen';
$GLOBALS['conf_dir_rechnung_intern'] = __DIR__ . '/../../httpdocs/pdf/rechnungen/intern';
$GLOBALS['conf_dir_rechnung_kunde'] = __DIR__ . '/../../httpdocs/pdf/rechnungen/kunde';