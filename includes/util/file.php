<?php

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

class File {
    public static function ensureDir($dir) {
        if (!is_dir($dir)) {
            $parent = dirname($dir);
            if (self::ensureDir($parent)) {
                return @mkdir($dir, 0777);
            }
        }
        return true;
    }
    
    public static function ensureTrailingSlash(&$dir) {
        $dir = trim($dir);
        $lastChar = substr($dir, -1, 1);
        if ($lastChar != '/' || $lastChar != "\\")
            $dir = $dir . DS;
    }
    
    public static function ensureLeadingSlash(&$dir) {
        $dir = trim($dir);
        $lastChar = substr($dir, 0, 1);
        if ($lastChar != '/' || $lastChar != "\\")
            $dir = DS.$dir;
    }
}