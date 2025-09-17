<?php
// Initialize template system
if (!defined('TEMPLATE_INIT')) {
    define('TEMPLATE_INIT', true);
    
    // Set content type and charset
    header('Content-Type: text/html; charset=UTF-8');
    
    // Enable error reporting for templates
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', 'Off');
    
    // Define common template functions
    if (!function_exists('safe_url')) {
        function safe_url($str) {
            return htmlspecialchars(urlencode($str), ENT_QUOTES, 'UTF-8');
        }
    }
    
    if (!function_exists('safe_html')) {
        function safe_html($str) {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }
    }
    
    if (!function_exists('is_valid_result')) {
        function is_valid_result($result) {
            return ($result && ($result instanceof mysqli_result));
        }
    }
}
?>
