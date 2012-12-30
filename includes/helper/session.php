<?php
class Session
{
    public $sessionName = 'hd';
    public $path = '/';
           
    private static $__instance;
    
    public static function instance() {
        if (self::$__instance == null) {
            self::$__instance = new self();
        }
        return self::$__instance;
    }
    
    public function __construct() {
        $this->_initHandler();
        ini_set('session.cookie_lifetime', 0);
        session_name($this->sessionName);
        $this->start();
    }
    
    public function __destruct() {
        $this->close();
    }
    
    
    protected function _initHandler() {
        $config = config('session');
        $engine = isset($config['handler']) ? $config['handler'] : '';
        if (empty($engine))
            return;
            
        $class = $engine . 'Session';
        if (!class_exists($class))
            return;
        $option = isset($config['option']) ? $config['option'] : array();
        $handler = new $class($option);
        session_set_save_handler(
            array($handler, 'open'),
            array($handler, 'close'),
            array($handler, 'read'),
            array($handler, 'write'),
            array($handler, 'destroy'),
            array($handler, 'gc')
        );
    }
    
    public function started() {
        return isset($_SESSION) && session_id();
    }
    
    public function start() {
        session_start();
    }
    
    public function destroy() {
        if ($this->started()) {
            session_destroy();
            $this->close();
        }
        $_SESSION = null;
        
    }
    
    public function close() {
        session_write_close();
    }
    
    public function renew() {
        if (session_id()) {
            if (session_id() != '' || isset($_COOKIE[session_name()])) {
                setcookie($this->sessionName, '', time() - 42000, $this->path);
            }
            session_regenerate_id(true);
        }
    }

    public function write($key, $value) {
        if ($this->started()) {
            $_SESSION[$key] = $value;
        }
    }

    public function read($key) {
        if (!$this->started() || !isset($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }
    
    public function check($key) {
        return ($this->started() && isset($_SESSION[$key]));
    }

    public function delete($key) {
        if ($this->started())
            unset($_SESSION[$key]);
    }
    
    public static function unserialize($session_data) {
        $method = ini_get("session.serialize_handler");
        switch ($method) {
            case "php":
                return self::unserialize_php($session_data);
                break;
            case "php_binary":
                return self::unserialize_phpbinary($session_data);
                break;
            default:
                throw new Exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
        }
    }

    private static function unserialize_php($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            if (!strstr(substr($session_data, $offset), "|")) {
                throw new Exception("invalid data, remaining: " . substr($session_data, $offset));
            }
            $pos = strpos($session_data, "|", $offset);
            $num = $pos - $offset;
            $varname = substr($session_data, $offset, $num);
            $offset += $num + 1;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }

    private static function unserialize_phpbinary($session_data) {
        $return_data = array();
        $offset = 0;
        while ($offset < strlen($session_data)) {
            $num = ord($session_data[$offset]);
            $offset += 1;
            $varname = substr($session_data, $offset, $num);
            $offset += $num;
            $data = unserialize(substr($session_data, $offset));
            $return_data[$varname] = $data;
            $offset += strlen(serialize($data));
        }
        return $return_data;
    }    
    
    
}

if(!interface_exists("SessionHandlerInterface")) {
	interface SessionHandlerInterface {
	/**
	 * Method called on open of a session.
	 *
	 * @return boolean Success
	 */
	    public function open();
	
	/**
	 * Method called on close of a session.
	 *
	 * @return boolean Success
	 */
	    public function close();
	
	/**
	 * Method used to read from a session.
	 *
	 * @param mixed $id The key of the value to read
	 * @return mixed The value of the key or false if it does not exist
	 */
	    public function read($id);
	
	/**
	 * Helper function called on write for sessions.
	 *
	 * @param integer $id ID that uniquely identifies session in database
	 * @param mixed $data The value of the data to be saved.
	 * @return boolean True for successful write, false otherwise.
	 */
	    public function write($id, $data);
	
	/**
	 * Method called on the destruction of a session.
	 *
	 * @param integer $id ID that uniquely identifies session in database
	 * @return boolean True for successful delete, false otherwise.
	 */
	    public function destroy($id);
	
	/**
	 * Run the Garbage collection on the session storage.  This method should vacuum all
	 * expired or dead sessions.
	 *
	 * @param integer $expires Timestamp (defaults to current time)
	 * @return boolean Success
	 */
	    public function gc($expires = null);
	}
}
