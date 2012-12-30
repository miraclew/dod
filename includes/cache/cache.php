<?php
/**
 * Caching for CakePHP.
 *
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
 * @package       Cake.Cache
 * @since         CakePHP(tm) v 1.2.0.4933
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class Cache {

    protected static $_engines = array();

    public static function config($name = null, $settings = array()) {
        if (!isset($name) || empty($settings['engine'])) {
            return false;
        }
        $engine = $settings['engine'];
        if (!isset(self::$_engines[$name])) {
            self::_buildEngine($name, $settings);
        }
    }

/**
 * Finds and builds the instance of the required engine class.
 *
 * @param string $name Name of the config array that needs an engine instance built
 * @return boolean
 * @throws CacheException
 */
    protected static function _buildEngine($name, $settings) {
        $cacheClass = $settings['engine'] . 'Engine';
        self::$_engines[$name] = new $cacheClass();
        if (self::$_engines[$name]->init($settings)) {
            return true;
        }
        return false;
    }


    public static function gc($config = 'default') {
        self::$_engines[$config]->gc();
    }
    
    
   /* public static function getRedis($config = 'default1') {
        if (!self::isInitialized($config)) {
            return false;
        }
        //debug(self::$_engines[$config]);
        return self::$_engines[$config]->getRedisInstance();
    }*/

    public static function write($key, $value, $duration = null, $config = 'default') {
        $settings = self::settings($config);
        if (empty($settings)) {
            return false;
        }
        if (!self::isInitialized($config)) {
            return false;
        }
        $key = self::$_engines[$config]->key($key);

        if (!$key || is_resource($value)) {
            return false;
        }
        if (!isset($duration)) $duration = $settings['duration'];
        
        $success = self::$_engines[$config]->write($settings['prefix'] . $key, $value, $duration);
        if ($success === false && $value !== '') {
            trigger_error(
                __("%s cache was unable to write '%s' to %s cache",
                    $config,
                    $key,
                    self::$_engines[$config]->settings['engine']
                ),
                E_USER_WARNING
            );
        }
        return $success;
    }


    public static function read($key, $config = 'default') {
        $settings = self::settings($config);

        if (empty($settings)) {
            return false;
        }
        if (!self::isInitialized($config)) {
            return false;
        }
        if (is_array($key)) {
            foreach($key as &$item) {
                $item = self::$_engines[$config]->key($item);
                if (!$item)
                    return false;
                $item = $settings['prefix'] . $item;
            }
        } else {
            $key = self::$_engines[$config]->key($key);
            if (!$key)
                return false;
            $key = $settings['prefix'] . $key;
        }

        return self::$_engines[$config]->read($key);
    }

/**
 * Increment a number under the key and return incremented value.
 *
 * @param string $key Identifier for the data
 * @param integer $offset How much to add
 * @param string $config Optional string configuration name. Defaults to 'default'
 * @return mixed new value, or false if the data doesn't exist, is not integer,
 *    or if there was an error fetching it.
 */
    public static function increment($key, $offset = 1, $config = 'default') {
        $settings = self::settings($config);

        if (empty($settings)) {
            return null;
        }
        if (!self::isInitialized($config)) {
            return false;
        }
        $key = self::$_engines[$config]->key($key);

        if (!$key || !is_integer($offset) || $offset < 0) {
            return false;
        }
        $success = self::$_engines[$config]->increment($settings['prefix'] . $key, $offset);
        return $success;
    }
/**
 * Decrement a number under the key and return decremented value.
 *
 * @param string $key Identifier for the data
 * @param integer $offset How much to subtract
 * @param string $config Optional string configuration name. Defaults to 'default'
 * @return mixed new value, or false if the data doesn't exist, is not integer,
 *   or if there was an error fetching it
 */
    public static function decrement($key, $offset = 1, $config = 'default') {
        $settings = self::settings($config);

        if (empty($settings)) {
            return null;
        }
        if (!self::isInitialized($config)) {
            return false;
        }
        $key = self::$_engines[$config]->key($key);

        if (!$key || !is_integer($offset) || $offset < 0) {
            return false;
        }
        $success = self::$_engines[$config]->decrement($settings['prefix'] . $key, $offset);
        return $success;
    }
/**
 * Delete a key from the cache.
 *
 * ### Usage:
 *
 * Deleting from the active cache configuration.
 *
 * `Cache::delete('my_data');`
 *
 * Deleting from a specific cache configuration.
 *
 * `Cache::delete('my_data', 'long_term');`
 *
 * @param string $key Identifier for the data
 * @param string $config name of the configuration to use. Defaults to 'default'
 * @return boolean True if the value was successfully deleted, false if it didn't exist or couldn't be removed
 */
    public static function delete($key, $config = 'default') {
        $settings = self::settings($config);

        if (empty($settings)) {
            return false;
        }
        if (!self::isInitialized($config)) {
            return false;
        }
        $key = self::$_engines[$config]->key($key);
        if (!$key) {
            return false;
        }

        $success = self::$_engines[$config]->delete($settings['prefix'] . $key);
        return $success;
    }

/**
 * Delete all keys from the cache.
 *
 * @param boolean $check if true will check expiration, otherwise delete all
 * @param string $config name of the configuration to use. Defaults to 'default'
 * @return boolean True if the cache was successfully cleared, false otherwise
 */
    public static function clear($check = false, $config = 'default') {
        if (!self::isInitialized($config)) {
            return false;
        }
        //TODO check expiration
        $success = self::$_engines[$config]->clear($check);
        return $success;
    }

/**
 * Check if Cache has initialized a working config for the given name.
 *
 * @param string $config name of the configuration to use. Defaults to 'default'
 * @return boolean Whether or not the config name has been initialized.
 */
    public static function isInitialized($config = 'default') {
        if (config('disablecache')) {
            return false;
        }
        return isset(self::$_engines[$config]);
    }

/**
 * Return the settings for the named cache engine.
 *
 * @param string $name Name of the configuration to get settings for. Defaults to 'default'
 * @return array list of settings for this engine
 * @see Cache::config()
 */
    public static function settings($name = 'default') {
        if (!empty(self::$_engines[$name])) {
            return self::$_engines[$name]->settings();
        }
        return array();
    }
    
    public static function getEngine($engineName) {
    	var_dump($engineName);
        foreach(self::$_engines as $name => $engine) {
        	//var_dump($name);
        	//var_dump($engine->settings['engine']);
            if ($engine->settings['engine'] == $engineName)
                return $engine;
        }
        return false;
    }
    
    public static function getEngineByConfig($config) {
    	if (isset(self::$_engines[$config]))
    		return self::$_engines[$config];
    	else
    		return false;
    }
}

/**
 * Storage engine for CakePHP caching
 *
 * @package       Cake.Cache
 */
abstract class CacheEngine {

/**
 * Settings of current engine instance
 *
 * @var array
 */
    public $settings = array();

/**
 * Initialize the cache engine
 *
 * Called automatically by the cache frontend
 *
 * @param array $settings Associative array of parameters for the engine
 * @return boolean True if the engine has been successfully initialized, false if not
 */
    public function init($settings = array()) {
        $this->settings = array_merge(
            array('prefix' => '', 'duration'=> 3600, 'probability'=> 100),
            $settings
        );
        if (!is_numeric($this->settings['duration'])) {
            $this->settings['duration'] = strtotime($this->settings['duration']) - time();
        }
        return true;
    }

/**
 * Garbage collection
 *
 * Permanently remove all expired and deleted data
 * @return void
 */
    public function gc() { }

/**
 * Write value for a key into cache
 *
 * @param string $key Identifier for the data
 * @param mixed $value Data to be cached
 * @param mixed $duration How long to cache for.
 * @return boolean True if the data was successfully cached, false on failure
 */
    abstract public function write($key, $value, $duration);

/**
 * Read a key from the cache
 *
 * @param string $key Identifier for the data
 * @return mixed The cached data, or false if the data doesn't exist, has expired, or if there was an error fetching it
 */
    abstract public function read($key);

/**
 * Increment a number under the key and return incremented value
 *
 * @param string $key Identifier for the data
 * @param integer $offset How much to add
 * @return New incremented value, false otherwise
 */
    abstract public function increment($key, $offset = 1);

/**
 * Decrement a number under the key and return decremented value
 *
 * @param string $key Identifier for the data
 * @param integer $offset How much to subtract
 * @return New incremented value, false otherwise
 */
    abstract public function decrement($key, $offset = 1);

/**
 * Delete a key from the cache
 *
 * @param string $key Identifier for the data
 * @return boolean True if the value was successfully deleted, false if it didn't exist or couldn't be removed
 */
    abstract public function delete($key);

/**
 * Delete all keys from the cache
 *
 * @param boolean $check if true will check expiration, otherwise delete all
 * @return boolean True if the cache was successfully cleared, false otherwise
 */
    abstract public function clear($check);

/**
 * Cache Engine settings
 *
 * @return array settings
 */
    public function settings() {
        return $this->settings;
    }

/**
 * Generates a safe key for use with cache engine storage engines.
 *
 * @param string $key the key passed over
 * @return mixed string $key or false
 */
    public function key($key) {
        if (empty($key)) {
            return false;
        }
        $key = str_replace(array(DS, '/', '.'), '_', strval($key));
        return $key;
    }
    

}
