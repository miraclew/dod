<?php

class Shell {
    public $name;
    public $args;       //存放解析后的不带名称的参数，如"main name"中main为cmd，name为参数
    public $params;     //存放解析后的带名称的参数，如"main -n name"中main为cmd，name为参数

    const VERBOSE = 2;
    const NORMAL = 1;
    const QUIET = 0;
        
/* $options配置的示例：
 * 第一级array代表指令，
 * 第二级array的第一个参数为0或1，0代表这个参数后面不跟具体的参数值，1代表后面需要指定参数值
 * 第二级array的第二个参数为参数描述，用于帮助
    public $options = array(
        'main' => array(
            '-b' => array(1, 'config file'),
            '-r' => array(1, 'repeat time'),
            '-t' => array(0, 'silent mode'),
        ),
        'update' => array(
            '-c' => array(1, 'config file'),
            '-r' => array(1, 'repeat time'),
            '-s' => array(0, 'silent mode'),
        )
    );
*/
    public $options;
    protected $_baseOptions = array(
        '-h' => array(0, 'show help'),
    );
    
    public function __construct() {
        //parent::__construct();
        
        if ($this->name == null) {
            $this->name = str_replace('Shell', '', get_class($this));
        }
    }
    
    protected function _parseArgs($args, $command = null) {
        $this->params = array();
        if (empty($command))
            $command = 'main';

        $options = isset($this->options[$command]) ? $this->options[$command] : array();
        $options = array_merge($options, $this->_baseOptions);
        $optionkeys = array_keys($options);

        $index = 0;
        $count = count($args);
        while($count > 0) {
            if (in_array($args[$index], $optionkeys)) {
                $key = str_replace('-', '', $args[$index]);
                if (!empty($options[$args[$index]][0])) {
                    $this->params[$key] = $args[$index+1];
                    array_splice($args, $index, 2);
                    $count -= 2;
                } else {
                    $this->params[$key] = 1;
                    array_splice($args, $index, 1);
                    $count -= 1;
                }
            } else if (substr($args[$index], 0, 1) == '-') {
                //去除不认识的option
                array_splice($args, $index, 1);
                $count -= 1;
            } else {
                $index += 1;
                $count -= 1;
            }
        }
        $this->args = $args;
    }
    
    public function cmd_main() {
        echo 'please implement your command method or implement cmd_main';
        return false;
    }
    
    
    public function runCommand($command, $args) {
        $method = 'cmd_' . $command;
        if (method_exists($this, $method)) {
            array_shift($args);
        } else {
            $command = 'main';
            $method = 'cmd_main';
        }
        
        $this->_parseArgs($args, $command);
        if (isset($this->params['h']) || isset($this->params['help'])) {
            $this->_help();
            return false;
        }
        return $this->{$method}();
        
    }
    
    protected function _help() {
        echo 'help is not implemented';
    }
    
    protected function out($message) {
        echo $message;
        echo "\n";
    }
}