<?php
/**
 * ShellDispatcher file
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
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Shell dispatcher handles dispatching cli commands.
 *
 * @package       Cake.Console
 */

class ShellDispatcher {

/**
 * Contains command switches parsed from the command line.
 *
 * @var array
 */
    public $params = array();

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 */
    public $args = array();

    public function __construct($args = array()) {
        set_time_limit(0);
        if (function_exists('ini_set')) {
            ini_set('html_errors', false);
            ini_set('implicit_flush', true);
            ini_set('max_execution_time', 0);
        }
        $this->parseParams($args);

    }
    

/**
 * Run the dispatcher
 *
 * @param array $argv The argv from PHP
 * @return void
 */
    public static function run($argv) {
        $dispatcher = new ShellDispatcher($argv);
        exit($dispatcher->dispatch() === false ? 1 : 0);
    }


/**
 * Dispatches a CLI request
 *
 * @return boolean
 * @throws MissingShellMethodException
 */
    public function dispatch() {
        $shell = array_shift($this->args);
        if (!$shell) {
            $this->help();
            return false;
        }
        if (in_array($shell, array('help', '--help', '-h'))) {
            $this->help();
            return true;
        }

        $Shell = $this->_getShell($shell);

        $command = null;
        if (isset($this->args[0])) {
            $command = $this->args[0];
        }

        if ($Shell instanceof Shell) {
            return $Shell->runCommand($command, $this->args);
        }
        $methods = get_class_methods($Shell);
        if (in_array('cmd_'.$command, $methods)) {
            return $Shell->{'cmd_'.$command}();
        } else {
            return $Shell->cmd_main();
        }
        //FIXME: check definition for MissingShellMethodException
        throw new MissingShellMethodException(array('shell' => $shell, 'method' => $command));
    }

/**
 * Get shell to use, either plugin shell or application shell
 *
 * All paths in the loaded shell paths are searched.
 *
 * @param string $shell Optionally the name of a plugin
 * @return mixed An object
 * @throws MissingShellException when errors are encountered.
 */
    protected function _getShell($shell) {
    	$ROOT_ = __DIR__.'/../../';
    	$INCLUDES_ = $ROOT_.'/includes/';
    	
        $class = ucfirst($shell) . 'Shell';
        include_once($INCLUDES_.'console/shell.php');
        $fileName = $ROOT_ . 'console/command/'.strtolower($class).'.php';
        if (!file_exists($fileName))
            $fileName = $INCLUDES_ . 'console/command/'.strtolower($class).'.php';
        if (file_exists($fileName))
            include_once($fileName);
        if (!class_exists($class)) {
            throw new MissingShellException(array(
                'class' => $class
            ));
        }
        $Shell = new $class();
        return $Shell;
    }

/**
 * Parses command line options and extracts the directory paths from $params
 *
 * @param array $args Parameters to parse
 * @return void
 */
    public function parseParams($args) {
        array_shift($args);         //移除第一个代表可执行文件的参数
        
        $index = 0;
        $count = count($args);
        while($count > 0) {
            if ($args[$index] == '-working') {
                $this->params['working'] = $args[$index+1];
                array_splice($args, $index, 2);
                $count -= 2;
            } else {
                $index += 1;
                $count -= 1;
            }
        }
        $this->args = $args;
    }

/**
 * Shows console help.  Performs an internal dispatch to the CommandList Shell
 *
 * @return void
 */
    public function help() {
        $this->args = array_merge(array('command_list'), $this->args);
        //TODO: 实现帮助
        //$this->dispatch();
    }
}
