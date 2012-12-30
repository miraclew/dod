<?php


class RedisEngine extends CacheEngine {

    protected $_Redis = null;

    public $settings = array();

    public function init($settings = array()) {
        if (!class_exists('Redis')) {
            return false;
        }
        if (isset($this->_Redis)) {
            return true;
        }
        parent::init(array_merge(array(
            'engine'=> 'Redis',
            'servers' => array('127.0.0.1'),
            ), $settings)
        );

        if (!is_array($this->settings['servers'])) {
            $this->settings['servers'] = array($this->settings['servers']);
        }
        
        $this->_Redis = new Redis();
        
        $serverAddr = $this->_parseServerString($this->settings['servers'][0]);
        
        $this->connect($serverAddr[0], $serverAddr[1]);
        return true;
    }

/**
 * Parses the server address into the host/port.  Handles both IPv6 and IPv4
 * addresses and Unix sockets
 *
 * @param string $server The server address string.
 * @return array Array containing host, port
 */

    protected function _parseServerString($server) {
        if ($server[0] == 'u') {
            return array($server, 0);
        }

        $position = strpos($server, ':');
        
        $port = 6379;
        $host = $server;
        if ($position !== false) {
            $host = substr($server, 0, $position);
            $port = substr($server, $position + 1);
        }
        return array($host, $port);
    }


    public function write($key, $value, $duration) {
        if ($duration > 30 * DAY) {
            $duration = 0;
        }

        return $this->_Redis->setex($key, $duration, $value);
    }


    public function read($key) {
        if (is_array($key))
            return $this->_Redis->mGet($key);
        else
            return $this->_Redis->get($key);
    }

/**
 * Increments the value of an integer cached key
 *
 * @param string $key Identifier for the data
 * @param integer $offset How much to increment
 * @return New incremented value, false otherwise
 * @throws CacheException when you try to increment with compress = true
 */
    public function increment($key, $offset = 1) {
        return $this->_Redis->incrBy($key, $offset);
    }

    public function decrement($key, $offset = 1) {
        return $this->_Redis->decrBy($key, $offset);
    }

    public function delete($key) {
        return $this->_Redis->delete($key);
    }

/**
 * Delete all keys from the cache
 *
 * @param boolean $check
 * @return boolean True if the cache was successfully cleared, false otherwise
 */
    public function clear($check) {
        if ($check) {
            return true;
        }
        $this->_Redis->flushDB();
        return true;
    }


    public function connect($host, $port = 6379) {
        if ($this->_Redis->connect($host, $port)) {        	
            return true;
        }
        return false;
    }
    public function getRedisInstance() {
        return $this->_Redis;
    }   
    
}
