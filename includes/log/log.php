<?php
/**
 * Logging.
 *
 * Log messages to text files.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Log
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Set up error level constants to be used within the framework if they are not defined within the
 * system.
 *
 */

    if (!defined('LOG_ERROR')) {
        define('LOG_ERROR', 2);
    }
    if (!defined('LOG_ERR')) {
        define('LOG_ERR', LOG_ERROR);
    }
    if (!defined('LOG_WARNING')) {
        define('LOG_WARNING', 3);
    }
    if (!defined('LOG_NOTICE')) {
        define('LOG_NOTICE', 4);
    }
    if (!defined('LOG_DEBUG')) {
        define('LOG_DEBUG', 5);
    }
    if (!defined('LOG_INFO')) {
        define('LOG_INFO', 6);
    }


class Log {

    protected static $_streams = array();

    //streamName should be lowercased
    public static function addLogger($streamName, $path = null) {
        $classname = ucfirst($streamName) . 'Log';
        $logger = new $classname($path);
        self::$_streams[$classname] = $logger;
    }
    
    public static function removeLoger($streamName) {
        unset(self::$_streams[$streamName]);
    }

    public static function write($message, $type='info') {
        $levels = array(
            LOG_WARNING => 'warning',
            LOG_NOTICE => 'notice',
            LOG_INFO => 'info',
            LOG_DEBUG => 'debug',
            LOG_ERR => 'error',
            LOG_ERROR => 'error'
        );
        if (is_array($message))
            $message = json_encode($message);
        if (is_int($type) && isset($levels[$type])) {
            $type = $levels[$type];
        }
        if (empty(self::$_streams)) {
            self::addLogger('file');
        }
        foreach (self::$_streams as $logger) {
            $logger->write($type, $message . "\r\n" );
        }
        return true;
    }

    public static function writeFile($message, $fileName='file.log', $path = '') {
        if (is_array($message))
            $message = json_encode($message);
        if (empty(self::$_streams)) {
            self::addLogger('file', $path);
        }
        foreach (self::$_streams as $logger) {
        	$now = date(DATEFORMAT, time());
        	str_replace('-', '_', $now);
        	$fileName .= '_' . $now; 
            $logger->writeFile($message . "\r\n", $fileName );
        }
        return true;
    }    
    
    public static function writeInfo($message) {
        return self::write($message, 'info');
    }
    
    public static function writeError($message) {
        return self::write($message, 'error');
    }
    
    public static function writeDebug($message) {
        return self::write($message, 'debug');
    }
}

interface LogInterface {
/**
 * Write method to handle writes being made to the Logger
 *
 * @param string $type
 * @param string $message
 * @return void
 */
    public function write($type, $message);
}
