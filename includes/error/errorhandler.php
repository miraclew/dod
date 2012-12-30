<?php
/**
 * Error handler
 *
 * Provides Error Capturing for Framework errors.
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
 * @package       Cake.Error
 * @since         CakePHP(tm) v 0.10.5.1732
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class ErrorHandler {

    public static function handleException(Exception $e) {
        $data = array(
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => '<pre>' . $e->getTraceAsString() . '</pre>',
            'type' => 'Exception',
            'class' => get_class($e)
        );
        
        //TODO: 正确处理debug与release版本的exception的显示
        if (config('debug')) {
            if ($e instanceof PDOException) {
                $data['sql'] = $e->queryString;
            }
            Debug::printDebugMessage($data);
        } else {
            echo $e->getMessage();
        }
    }

    public static function handleError($code, $description, $file = null, $line = null, $context = null) {
        if (error_reporting() === 0) {
            return false;
        }
        $data = array(
            'message' => $description,
            'file' => $file,
            'line' => $line
        );
 
        list($error, $log) = self::mapErrorCode($code);

        if (config('debug')) {
            $data = array(
                'type' => $error,
                'message' => $description,
                'file' => $file,
                'line' => $line,
            );
            Debug::printDebugMessage($data);
        } else {
            $message = $error . ' (' . $code . '): ' . $description . ' in [' . $file . ', line ' . $line . ']';
            return Log::write($message, $log);
        }
    }

/**
 * Map an error code into an Error word, and log location.
 *
 * @param integer $code Error code to map
 * @return array Array of error word, and log location.
 */
    public static function mapErrorCode($code) {
        $error = $log = null;
        switch ($code) {
            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $error = 'Error';
                $log = LOG_ERROR;
            break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $error = 'Warning';
                $log = LOG_WARNING;
            break;
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_DEPRECATED:
            case E_STRICT:
                $error = 'Notice';
                $log = LOG_NOTICE;
            break;

        }
        return array($error, $log);
    }
}
