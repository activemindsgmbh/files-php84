<?php
declare(strict_types=1);

/**
 * Main configuration file
 * IMPORTANT: Update these values for your production environment
 */

// Database Configuration
$GLOBALS['conf_mysql_db']   = 'your_database_name';    // Replace with your database name
$GLOBALS['conf_mysql_host'] = 'your_database_host';    // Replace with your database host
$GLOBALS['conf_mysql_user'] = 'your_database_user';    // Replace with your database username
$GLOBALS['conf_mysql_pass'] = 'your_database_pass';    // Replace with your database password

// Application Settings
$GLOBALS['conf_umsatzsteuer'] = 19.0;
$GLOBALS['conf_default_waehrung'] = 'EUR';
$GLOBALS['conf_default_zahlungsziel'] = 7;
$GLOBALS['conf_email_return_path'] = 'your_email@example.com';

// File Paths (Update these for your server)
$GLOBALS['conf_logo'] = '/var/www/vhosts/your-domain/data/logo.png';
$GLOBALS['conf_dir_mahnung'] = '/var/www/vhosts/your-domain/httpdocs/pdf/mahnungen';
$GLOBALS['conf_dir_rechnung_intern'] = '/var/www/vhosts/your-domain/httpdocs/pdf/rechnungen/intern';
$GLOBALS['conf_dir_rechnung_kunde'] = '/var/www/vhosts/your-domain/httpdocs/pdf/rechnungen/kunde';

// URLs (Update these for your domain)
$GLOBALS['conf_url_mahnung'] = '/pdf/mahnungen';
$GLOBALS['conf_url_rechnung_intern'] = '/pdf/rechnungen/intern';
$GLOBALS['conf_url_rechnung_kunde'] = '/pdf/rechnungen/kunde';

// Error Handling
$GLOBALS['conf_error_prefix'] = '<div class="errormsg">';
$GLOBALS['conf_error_suffix'] = '</div>';

// Load environment-specific configuration if it exists
$env_config = __DIR__ . '/config.' . (getenv('APP_ENV') ?: 'production') . '.inc.php';
if (file_exists($env_config)) {
    require_once($env_config);
}