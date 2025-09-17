<?php

/**
 * StringUtils class provides modern replacements for deprecated string functions
 * and ensures proper encoding handling for PHP 8.4
 */
class StringUtils {
    /**
     * Case-insensitive regular expression match
     */
    public static function regexMatch(string $pattern, string $subject): bool {
        return (bool)preg_match('/' . preg_quote($pattern, '/') . '/i', $subject);
    }

    /**
     * Case-insensitive regular expression match with capture groups
     */
    public static function regexMatchWithCapture(string $pattern, string $subject, &$matches): bool {
        return (bool)preg_match('/' . preg_quote($pattern, '/') . '/i', $subject, $matches);
    }

    /**
     * Safe HTML special characters conversion with proper encoding
     */
    public static function htmlSpecialChars(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Properly encode strings to UTF-8
     */
    public static function toUtf8(string $string): string {
        if (!mb_check_encoding($string, 'UTF-8')) {
            return mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
        }
        return $string;
    }

    /**
     * Check if a string ends with another string (case insensitive)
     */
    public static function endsWith(string $haystack, string $needle): bool {
        return str_ends_with(strtolower($haystack), strtolower($needle));
    }

    /**
     * Modern replacement for the deprecated ereg/eregi functions
     */
    public static function checkTLD(string $domain): string {
        $domain = strtolower(trim($domain));
        $tlds = [
            'at' => '/\.at$/',
            'biz' => '/\.biz$/',
            'ch' => '/\.ch$/',
            'co.uk' => '/\.co\.uk$/',
            'com' => '/\.com$/',
            'de' => '/\.de$/',
            'fr' => '/\.fr$/',
            'info' => '/\.info$/',
            'net' => '/\.net$/',
            'org' => '/\.org$/',
            'ws' => '/\.ws$/'
        ];

        foreach ($tlds as $tld => $pattern) {
            if (preg_match($pattern, $domain)) {
                return $tld;
            }
        }
        return '';
    }

    /**
     * Validate email address using modern pattern
     */
    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Parse date string with proper type handling
     */
    public static function parseDate(string $date): int {
        if (preg_match('/^([0-9]{1,2})[-\/.,;.: ]([0-9]{1,2})[-\/.,;.: ]([0-9]{4})$/', $date, $matches)) {
            return (int)sprintf('%04d%02d%02d', $matches[3], $matches[2], $matches[1]);
        }
        return 0;
    }
}
?>