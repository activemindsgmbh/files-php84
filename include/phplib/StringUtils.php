<?php
declare(strict_types=1);

class StringUtils {
    public static function toUtf8(string $string): string {
        if (!mb_check_encoding($string, 'UTF-8')) {
            return mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));
        }
        return $string;
    }

    public static function htmlSpecialChars(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function checkTLD(string $domain): string {
        $domain = strtolower(trim($domain));
        $tlds = [
            'at' => '/\.at$/i',
            'biz' => '/\.biz$/i',
            'ch' => '/\.ch$/i',
            'co.uk' => '/\.co\.uk$/i',
            'com' => '/\.com$/i',
            'de' => '/\.de$/i',
            'fr' => '/\.fr$/i',
            'info' => '/\.info$/i',
            'net' => '/\.net$/i',
            'org' => '/\.org$/i',
            'ws' => '/\.ws$/i'
        ];

        foreach ($tlds as $tld => $pattern) {
            if (preg_match($pattern, $domain)) {
                return $tld;
            }
        }
        return '';
    }

    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
