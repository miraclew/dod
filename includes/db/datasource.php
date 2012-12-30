<?php

class DataSource extends Object {

    public $configKeyName = null;
    
    public $connected = false;
    
    protected $_lastErr = false;

 /**
 * Holds references to descriptions loaded by the DataSource
 *
 * @var array
 */
    protected $_descriptions = array();
    
/**
 * Holds a list of sources (tables) contained in the DataSource
 *
 * @var array
 */
    protected $_sources = null;
    

    
/**
 * The DataSource configuration
 *
 * @var array
 */
    public $config = array();

/**
 * Whether or not this DataSource is in the middle of a transaction
 *
 * @var boolean
 */
    protected $_transactionStarted = false;

/**
 * Whether or not source data like available tables and schema descriptions
 * should be cached
 *
 * @var boolean
 */
    public $cacheSources = true;
    
/**
 * Constructor.
 *
 * @param array $config Array of configuration information for the datasource.
 */
    public function __construct($config = array()) {
        parent::__construct();
        $this->config = array_merge($this->_baseConfig, $config);
    }

/**
 * Caches/returns cached results for child instances
 *
 * @param mixed $data
 * @return array Array of sources available in this datasource.
 */
    public function listSources($data = null) {
        if ($this->cacheSources === false) {
            return null;
        }

        if ($this->_sources !== null) {
            return $this->_sources;
        }

        $key = $this->configKeyName . '_' . $this->config['database'] . '_list';
        $key = preg_replace('/[^A-Za-z0-9_\-.+]/', '_', $key);
        
        if (empty($data)) {
            $sources = Cache::read($key, '_hd_model_');
        } else {
            $sources = $data;
            Cache::write($key, $data, null, '_hd_model_');
        }

        return $this->_sources = $sources;
    }

/**
 * Returns a Model description (metadata) or null if none found.
 *
 * @param Model|string $model
 * @return array Array of Metadata for the $model
 */
    public function describe($table) {
        if ($this->cacheSources === false) {
            return null;
        }
        if (isset($this->_descriptions[$table])) {
            return $this->_descriptions[$table];
        }
                
        $cache = $this->_cacheDescription($table);
        return $cache;
    }
    
    
/**
 * Begin a transaction
 *
 * @return boolean Returns true if a transaction is not in progress
 */
    public function begin() {
        return !$this->_transactionStarted;
    }

/**
 * Commit a transaction
 *
 * @return boolean Returns true if a transaction is in progress
 */
    public function commit() {
        return $this->_transactionStarted;
    }

/**
 * Rollback a transaction
 *
 * @return boolean Returns true if a transaction is in progress
 */
    public function rollback() {
        return $this->_transactionStarted;
    }

/**
 * Converts column types to basic types
 *
 * @param string $real Real  column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 */
    public function column($real) {
        return false;
    }

/**
 * Used to create new records. The "C" CRUD.
 *
 * To-be-overridden in subclasses.
 *
 * @param Model $model The Model to be created.
 * @param array $fields An Array of fields to be saved.
 * @param array $values An Array of values to save.
 * @return boolean success
 */
    public function create(Model $model, $data) {
        return false;
    }

/**
 * Used to read records from the Datasource. The "R" in CRUD
 *
 * To-be-overridden in subclasses.
 *
 * @param Model $model The model being read.
 * @param array $queryData An array of query data used to find the data you want
 * @return mixed
 */
    public function read(Model $model, $queryData = array()) {
        return false;
    }

/**
 * Update a record(s) in the datasource.
 *
 * To-be-overridden in subclasses.
 *
 * @param Model $model Instance of the model class being updated
 * @param array $fields Array of fields to be updated
 * @param array $values Array of values to be update $fields to.
 * @return boolean Success
 */
    public function update(Model $model, $data) {
        return false;
    }

/**
 * Delete a record(s) in the datasource.
 *
 * To-be-overridden in subclasses.
 *
 * @param Model $model The model class having record(s) deleted
 * @param mixed $conditions The conditions to use for deleting.
 * @return void
 */
    public function delete(Model $model, $conditions) {
        return false;
    }

/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param mixed $source
 * @return mixed Last ID key generated in previous INSERT
 */
    public function lastInsertId($source = null) {
        return false;
    }

/**
 * Returns the number of rows returned by last operation.
 *
 * @param mixed $source
 * @return integer Number of rows returned by last operation
 */
    public function lastNumRows($source = null) {
        return false;
    }

/**
 * Returns the number of rows affected by last query.
 *
 * @param mixed $source
 * @return integer Number of rows affected by last query.
 */
    public function lastAffected($source = null) {
        return false;
    }

/**
 * Check whether the conditions for the Datasource being available
 * are satisfied.  Often used from connect() to check for support
 * before establishing a connection.
 *
 * @return boolean Whether or not the Datasources conditions for use are met.
 */
    public function enabled() {
        return true;
    }

/**
 * Sets the configuration for the DataSource.
 * Merges the $config information with the _baseConfig and the existing $config property.
 *
 * @param array $config The configuration array
 * @return void
 */
    public function setConfig($config = array()) {
        $this->config = array_merge($this->_baseConfig, $this->config, $config);
    }

/**
 * Cache the DataSource description
 *
 * @param string $object The name of the object (model) to cache
 * @param mixed $data The description of the model, usually a string or array
 * @return mixed
 */
    protected function _cacheDescription($object, $data = null) {
        if ($this->cacheSources === false) {
            return null;
        }
        $key = $this->configKeyName . '_' . $object;
        if ($data) {
            $cache = $data;
            Cache::write($key, $cache, null, '_hd_model_');
            $this->_descriptions[$object] =& $cache;
        } else {
            $cache = Cache::read($key, '_hd_model_');
            if ($cache != null) {
                $this->_descriptions[$object] =& $cache;
            }
        }

        return $cache;
    }
    
/**
 * Closes the current datasource.
 *
 */
    public function __destruct() {
        if ($this->_transactionStarted) {
            $this->rollback();
        }
        if ($this->connected) {
            $this->close();
        }
    }
}

