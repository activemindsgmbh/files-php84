<?php
declare(strict_types=1);

class Constants {
    public const DB_FETCH_BOTH = MYSQLI_BOTH;
    public const DB_FETCH_ASSOC = MYSQLI_ASSOC;
    public const DB_FETCH_NUM = MYSQLI_NUM;

    public const ENCODING_UTF8 = 'UTF-8';
    public const ENCODING_LATIN1 = 'ISO-8859-1';

    public const HTML_ENTITIES_FLAGS = ENT_QUOTES | ENT_HTML5;
    
    public const DATE_FORMAT_DB = 'Y-m-d H:i:s';
    public const DATE_FORMAT_DISPLAY = 'd.m.Y';
}
