<?php


class DBManager {

    protected $_config = null;
    protected $_schema = null;
    

    protected $_dataSources = array();
    
    protected $_init = false;
    
    private static $__instance = null;
    
    /** 
     * @return DBManager
     */
    public function instance() {
        if (empty(self::$__instance)) {
            self::$__instance = new self();
        }
        return self::$__instance;
        
    }

    public function __construct() {
        $this->_init();
    }
    protected function _init() {
        if ($this->_init)
            return;
            
        $configFile = ROOT_ . 'config/database.php';
        if (file_exists($configFile)) {
            include_once $configFile;
            if (class_exists('DBConfig')) {
                $this->_config = new DBConfig();
            }
        }
        $schemaFile = ROOT_ . 'config/schema/schema.php';
        if (file_exists($schemaFile)) {
            include_once $schemaFile;
            if (class_exists('Schema')) {
                $this->_schema = new Schema();
            }
        }
        $this->_init = true;
    }
    
    public function getDBSchema() {
        return $this->_schema;
    }
    
    public function getDBConfig() {
        return $this->_config;
    }

/**
 * Gets a reference to a DataSource object
 *
 * @param string $name The name of the DataSource, as defined in app/Config/database.php
 * @return DataSource Instance
 * @throws MissingDatasourceConfigException
 * @throws MissingDatasourceException
 */
    public function getDataSource($name) {
        if (!empty($this->_dataSources[$name])) {
            return $this->_dataSources[$name];
        }
        if (!isset($this->_config->{$name})) {
            //TODO error handler
            throw new MissingDatasourceConfigException(array($name));
        }
        $configData = $this->_config->{$name};
        $class = $configData['datasource'];
        if (!class_exists($class)) {
            throw new MissingDatasourceException(array($class));
        }
        $this->_dataSources[$name] = new $class($this->_config->{$name});
        $this->_dataSources[$name]->configKeyName = $name;
        return $this->_dataSources[$name];
    }


    public function sourceList() {
        return array_keys($this->_dataSources);
    }

}

