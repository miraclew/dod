<?php
/**
 * File Storage stream for Logging
 *
 * PHP 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       Cake.Log.Engine
 * @since         CakePHP(tm) v 1.3
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class FileLog implements LogInterface {

    protected $_path = null;
    protected $_debugTypes = array('notice', 'info', 'debug');

    public function __construct($path) {
        global $dlConfig;
        if (isset($dlConfig['log_path']))
        	$this->_path = !empty($path) ? $path : $dlConfig['log_path'];
        else
        	$this->_path = !empty($path) ? $path : LOG_;
    }


    public function write($type, $message) {
        if ($type == 'error' || $type == 'warning') {
            $filename = $this->_path  . 'error.log';
        } elseif (in_array($type, $this->_debugTypes)) {
            $filename = $this->_path . 'debug.log';
        } else {
            $filename = $this->_path . $type . '.log';
        }
        $output = date('Y-m-d H:i:s') . ' ' . ucfirst($type) . ': ' . $message . "\n";
        return file_put_contents($filename, $output, FILE_APPEND);
    }
    public function writeFile($message, $logFileName) {
        $filename = $this->_path  . $logFileName;
        $output = date('Y-m-d H:i:s') . ' '  . $message ;
        return file_put_contents($filename, $output, FILE_APPEND);
    }
}
