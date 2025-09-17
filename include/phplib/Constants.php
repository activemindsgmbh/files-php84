<?php

/**
 * Constants class provides centralized management of application constants
 * and replaces deprecated PHP constants and magic numbers
 */
class Constants {
    // Database related
    public const DB_FETCH_BOTH = MYSQLI_BOTH;
    public const DB_FETCH_ASSOC = MYSQLI_ASSOC;
    public const DB_FETCH_NUM = MYSQLI_NUM;

    // Error reporting levels (replacing deprecated E_* constants)
    public const ERROR_ALL = E_ALL;
    public const ERROR_STRICT = E_STRICT;
    public const ERROR_DEPRECATED = E_DEPRECATED;

    // File operations
    public const FILE_READ = 'r';
    public const FILE_WRITE = 'w';
    public const FILE_APPEND = 'a';
    public const FILE_BINARY = 'b';

    // Character encodings
    public const ENCODING_UTF8 = 'UTF-8';
    public const ENCODING_LATIN1 = 'ISO-8859-1';
    public const ENCODING_ASCII = 'ASCII';

    // HTML entities
    public const HTML_ENTITIES_FLAGS = ENT_QUOTES | ENT_HTML5;
    public const HTML_ENTITIES_ENCODING = self::ENCODING_UTF8;

    // Date formats
    public const DATE_FORMAT_DB = 'Y-m-d H:i:s';
    public const DATE_FORMAT_DISPLAY = 'd.m.Y';
    public const DATE_FORMAT_TIME = 'H:i:s';
    
    // Regular expressions
    public const REGEX_EMAIL = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    public const REGEX_DATE = '/^([0-9]{1,2})[-\/.,;.: ]([0-9]{1,2})[-\/.,;.: ]([0-9]{4})$/';
    
    // HTTP status codes
    public const HTTP_OK = 200;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_SERVER_ERROR = 500;
}
?>