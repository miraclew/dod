<?php
/**
 * Dbo Source: this class is a brief version of CakePHP DboSource class
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
 * @package       Cake.Model.Datasource
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


/**
 * DboSource
 *
 * Creates DBO-descendant objects from a given db connection configuration
 *
 * @package       Cake.Model.Datasource
 */
class Mysql extends DataSource {

    public $description = "MySQL DBO Driver";

    //Base configuration settings for MySQL driver
    protected $_baseConfig = array(
        'persistent' => true,
        'host' => 'localhost',
        'login' => 'root',
        'password' => '',
        'database' => 'defaultdb',
        'port' => '3306'
    );
    
    //boolean: set true to print full query debug info
    public $fullDebug = false;


    //string: to hold how many rows were affected by the last SQL operation.
    public $affected = null;
    public $numRows = null;
    
    
    public $alias = 'AS ';
    public $startQuote = "`";
    public $endQuote = "`";

    //Time the last query took
    public $took = null;
    //Queries count.
    protected $_queriesCount = 0;
    //Total duration of all queries.
    protected $_queriesTime = null;
    //Log of queries executed by this DataSource
    protected $_queriesLog = array();
    //Maximum number of items in query log
    protected $_queriesLogMax = 200;


/**
 * A reference to the physical connection of this DataSource
 *
 * @var array
 */
    protected $_connection = null;
    
/**
 * Result
 *
 * @var array
 */
    protected $_result = null;
    


    public static $methodCache = array();
    public $cacheMethods = true;
    protected $_methodCacheChange = false;

    //Integer: Indicates the level of nested transactions
    protected $_transactionNesting = 0;

/**
 * Index of basic SQL commands
 *
 * @var array
 */
    protected $_commands = array(
        'begin'    => 'START TRANSACTION',
        'commit'   => 'COMMIT',
        'rollback' => 'ROLLBACK'
    );

/**
 * List of table engine specific parameters used on table creating
 *
 * @var array
 */
    public $tableParameters = array(
        'charset' => array('value' => 'DEFAULT CHARSET', 'quote' => false, 'join' => '=', 'column' => 'charset'),
        'collate' => array('value' => 'COLLATE', 'quote' => false, 'join' => '=', 'column' => 'Collation'),
        'engine' => array('value' => 'ENGINE', 'quote' => false, 'join' => '=', 'column' => 'Engine')
    );

/**
 * List of engine specific additional field parameters used on table creating
 *
 * @var array
 */
    public $fieldParameters = array(
        'charset' => array('value' => 'CHARACTER SET', 'quote' => false, 'join' => ' ', 'column' => false, 'position' => 'beforeDefault'),
        'collate' => array('value' => 'COLLATE', 'quote' => false, 'join' => ' ', 'column' => 'Collation', 'position' => 'beforeDefault'),
        'comment' => array('value' => 'COMMENT', 'quote' => true, 'join' => ' ', 'column' => 'Comment', 'position' => 'afterDefault')
    );
    
/**
 * MySQL column definition
 *
 * @var array
 */
    public $columns = array(
        'primary_key' => array('name' => 'NOT NULL AUTO_INCREMENT'),
        'string' => array('name' => 'varchar', 'limit' => '255'),
        'text' => array('name' => 'text'),
        'integer' => array('name' => 'int', 'limit' => '11', 'formatter' => 'intval'),
        'float' => array('name' => 'float', 'formatter' => 'floatval'),
        'datetime' => array('name' => 'datetime', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
        'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
        'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
        'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
        'binary' => array('name' => 'blob'),
        'boolean' => array('name' => 'tinyint', 'limit' => '1')
    );

    public $index = array('PRI' => 'primary', 'MUL' => 'index', 'UNI' => 'unique');

/**
 * Constructor
 *
 * @param array $config Array of configuration information for the Datasource.
 * @param boolean $autoConnect Whether or not the datasource should automatically connect.
 */
    public function __construct($config = null, $autoConnect = true) {
        if (!isset($config['prefix'])) {
            $config['prefix'] = '';
        }
        
        parent::__construct($config);
        $this->fullDebug = config('debug') > 1;
        if (!$this->enabled()) {
            throw new MissingConnectionException(array(
                'class' => get_class($this)
            ));
        }
        if ($autoConnect) {
            $this->connect();
        }
    }

/**
 * Connects to the database using options in the given configuration array.
 *
 * @return boolean True if the database could be connected, else false
 * @throws MissingConnectionException
 */
    public function connect() {
        $config = $this->config;
        $this->connected = false;
        try {
            $flags = array(
                PDO::ATTR_PERSISTENT => $config['persistent'],
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            	PDO::ATTR_EMULATE_PREPARES => false
            );
            if (!empty($config['encoding'])) {
                $flags[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $config['encoding'];
            }
            if (empty($config['unix_socket'])) {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
            } else {
                $dsn = "mysql:unix_socket={$config['unix_socket']};dbname={$config['database']}";
            }
            $this->_connection = new PDO(
                $dsn,
                $config['login'],
                $config['password'],
                $flags
            );
            $this->connected = true;
        } catch (PDOException $e) {
            throw new MissingConnectionException(array('class' => $e->getMessage()));
        }

        return $this->connected;
    }

/**
 * Checks if the source is connected to the database.
 *
 * @return boolean True if the database is connected, else false
 */
    public function isConnected() {
        return $this->connected;
    }
    
/**
 * Check whether the MySQL extension is installed/loaded
 *
 * @return boolean
 */
    public function enabled() {
        return in_array('mysql', PDO::getAvailableDrivers());
    }
        
/**
 * Reconnects to database server with optional new settings
 *
 * @param array $config An array defining the new configuration settings
 * @return boolean True on success, false on failure
 */
    public function reconnect($config = array()) {
        $this->disconnect();
        $this->setConfig($config);

        return $this->connect();
    }

/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 */
    public function disconnect() {
        if ($this->_result instanceof PDOStatement) {
            $this->_result->closeCursor();
        }
        unset($this->_connection);
        $this->connected = false;
        return true;
    }

/**
 * Get the underlying connection object.
 *
 * @return PDOConnection
 */
    public function getConnection() {
        return $this->_connection;
    }
    
    
/**
 * Gets the database encoding
 *
 * @return string The database encoding
 */
    public function getEncoding() {
        return $this->_execute('SHOW VARIABLES LIKE ?', array('character_set_client'))->fetchObject()->Value;
    }
    
/**
 * Sets the database encoding
 *
 * @param string $enc Database encoding
 * @return boolean
 */
    public function setEncoding($enc) {
        return $this->_execute('SET NAMES ' . $enc) !== false;
    }

/**
 * Gets the version string of the database server
 *
 * @return string The database encoding
 */
    public function getVersion() {
        return $this->_connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    }
        

/**
 * Query charset by collation
 *
 * @param string $name Collation name
 * @return string Character set name
 */
    public function getCharsetName($name) {
        if ((bool)version_compare($this->getVersion(), "5", ">=")) {
            $r = $this->_execute('SELECT CHARACTER_SET_NAME FROM INFORMATION_SCHEMA.COLLATIONS WHERE COLLATION_NAME = ?', array($name));
            $cols = $r->fetch();

            if (isset($cols['CHARACTER_SET_NAME'])) {
                return $cols['CHARACTER_SET_NAME'];
            }
        }
        return false;
    }

/**
 * Returns an array of sources (tables) in the database.
 *
 * @param mixed $data
 * @return array Array of tablenames in the database
 */
    public function listSources($data = null) {
        $cache = parent::listSources();
        if ($cache != null) {
            return $cache;
        }
        $result = $this->_execute('SHOW TABLES FROM ' . $this->config['database']);

        if (!$result) {
            $result->closeCursor();
            return array();
        } else {
            $tables = array();

            while ($line = $result->fetch()) {
                $tables[] = $line[0];
            }
            
            $result->closeCursor();
            parent::listSources($tables);
            return $tables;
        }
    }
    
/**
 * Returns an array of the fields in given table name.
 *
 * @param Model|string $model Name of database table to inspect or model instance
 * @return array Fields in table. Keys are name and type
 * @throws CakeException
 */
    public function describe($table, $useConfig = false) {
        $table = $this->fullTableName($table);
        $cache = parent::describe($table);
        if ($cache != null) {
            return $cache;
        }

        $fields = false;
        $cols = $this->_execute('SHOW FULL COLUMNS FROM ' . $table);
        if (!$cols) {
            throw new HException(__('Could not describe table for %s', $table));
        }
        //foreach ($cols as $column) {
        while($column = $cols->fetch(PDO::FETCH_OBJ)) {
            $fields[$column->Field] = array(
                'type' => $this->column($column->Type),
                'null' => ($column->Null === 'YES' ? true : false),
                'default' => $column->Default,
                'length' => $this->length($column->Type),
            );
            	//王珂 king 2012.9.20 为了判断 字段是不是为负数
	        if ( 'integer' == $fields[$column->Field]['type'] && strpos($column->Type, 'unsigned') !== false) {
	           $fields[$column->Field]['unsigned']	= TRUE;
	        }
            if (!empty($column->Key) && isset($this->index[$column->Key])) {
                $fields[$column->Field]['key'] = $this->index[$column->Key];
            }
            foreach ($this->fieldParameters as $name => $value) {
                if (!empty($column->{$value['column']})) {
                    $fields[$column->Field][$name] = $column->{$value['column']};
                }
            }
            if (isset($fields[$column->Field]['collate'])) {
                $charset = $this->getCharsetName($fields[$column->Field]['collate']);
                if ($charset) {
                    $fields[$column->Field]['charset'] = $charset;
                }
            }
        }
        $cols->closeCursor();
        $this->_cacheDescription($table, $fields);
        return $fields;
    }
    

/**
 * Converts database-layer column types to basic types, Only used in describe
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 */
    public function column($real) {
        if (is_array($real)) {
            $col = $real['name'];
            if (isset($real['limit'])) {
                $col .= '(' . $real['limit'] . ')';
            }
            return $col;
        }

        $col = str_replace(')', '', $real);
        $limit = $this->length($real);
        if (strpos($col, '(') !== false) {
            list($col, $vals) = explode('(', $col);
        }

        if (in_array($col, array('date', 'time', 'datetime', 'timestamp'))) {
            return $col;
        }
        if (($col === 'tinyint' && $limit == 1) || $col === 'boolean') {
            return 'boolean';
        }
        if (strpos($col, 'int') !== false) {
            return 'integer';
        }
        if (strpos($col, 'char') !== false || $col === 'tinytext') {
            return 'string';
        }
        if (strpos($col, 'text') !== false) {
            return 'text';
        }
        if (strpos($col, 'blob') !== false || $col === 'binary') {
            return 'binary';
        }
        if (strpos($col, 'float') !== false || strpos($col, 'double') !== false || strpos($col, 'decimal') !== false) {
            return 'float';
        }
        if (strpos($col, 'enum') !== false) {
            return "enum($vals)";
        }
        return 'text';
    }
    

/**
 * Gets the length of a database-native column description, or null if no length
 * Only used in column() and describe()
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return mixed An integer or string representing the length of the column, or null for unknown length.
 */
    public function length($real) {
        if (!preg_match_all('/([\w\s]+)(?:\((\d+)(?:,(\d+))?\))?(\sunsigned)?(\szerofill)?/', $real, $result)) {
            $col = str_replace(array(')', 'unsigned'), '', $real);
            $limit = null;

            if (strpos($col, '(') !== false) {
                list($col, $limit) = explode('(', $col);
            }
            if ($limit !== null) {
                return intval($limit);
            }
            return null;
        }

        $types = array(
            'int' => 1, 'tinyint' => 1, 'smallint' => 1, 'mediumint' => 1, 'integer' => 1, 'bigint' => 1
        );

        list($real, $type, $length, $offset, $sign, $zerofill) = $result;
        $typeArr = $type;
        $type = $type[0];
        $length = $length[0];
        $offset = $offset[0];

        $isFloat = in_array($type, array('dec', 'decimal', 'float', 'numeric', 'double'));
        if ($isFloat && $offset) {
            return $length . ',' . $offset;
        }

        if (($real[0] == $type) && (count($real) === 1)) {
            return null;
        }

        if (isset($types[$type])) {
            $length += $types[$type];
            if (!empty($sign)) {
                $length--;
            }
        } elseif (in_array($type, array('enum', 'set'))) {
            $length = 0;
            foreach ($typeArr as $key => $enumValue) {
                if ($key === 0) {
                    continue;
                }
                $tmpLength = strlen($enumValue);
                if ($tmpLength > $length) {
                    $length = $tmpLength;
                }
            }
        }
        return intval($length);
    }
    

    
/**
 * Returns a quoted name of $data for use in an SQL statement.
 * Strips fields out of SQL functions before quoting.
 *
 * Results of this method are stored in a memory cache.  This improves performance, but
 * because the method uses a simple hashing algorithm it can infrequently have collisions.
 * Setting DboSource::$cacheMethods to false will disable the memory cache.
 *
 * @param mixed $data Either a string with a column to quote. An array of columns to quote or an
 *   object from DboSource::expression() or DboSource::identifier()
 * @return string SQL field
 */
    public function name($data) {
        if ($data === '*') {
            return '*';
        }
        if (is_array($data)) {
            foreach ($data as $i => $dataItem) {
                $data[$i] = $this->name($dataItem);
            }
            return $data;
        }
        $cacheKey = crc32($this->startQuote.$data.$this->endQuote);
        if ($return = $this->cacheMethod(__FUNCTION__, $cacheKey)) {
            return $return;
        }
        $data = trim($data);
        if (preg_match('/^[\w-]+(?:\.[^ \*]*)*$/', $data)) { // string, string.string
            if (strpos($data, '.') === false) { // string
                return $this->cacheMethod(__FUNCTION__, $cacheKey, $this->startQuote . $data . $this->endQuote);
            }
            $items = explode('.', $data);
            return $this->cacheMethod(__FUNCTION__, $cacheKey,
                $this->startQuote . implode($this->endQuote . '.' . $this->startQuote, $items) . $this->endQuote
            );
        }
        if (preg_match('/^[\w-]+\.\*$/', $data)) { // string.*
            return $this->cacheMethod(__FUNCTION__, $cacheKey,
                $this->startQuote . str_replace('.*', $this->endQuote . '.*', $data)
            );
        }
        if (preg_match('/^([\w-]+)\((.*)\)$/', $data, $matches)) { // Functions
            return $this->cacheMethod(__FUNCTION__, $cacheKey,
                 $matches[1] . '(' . $this->name($matches[2]) . ')'
            );
        }
        if (
            preg_match('/^([\w-]+(\.[\w-]+|\(.*\))*)\s+' . preg_quote($this->alias) . '\s*([\w-]+)$/i', $data, $matches
        )) {
            return $this->cacheMethod(
                __FUNCTION__, $cacheKey,
                preg_replace(
                    '/\s{2,}/', ' ', $this->name($matches[1]) . ' ' . $this->alias . ' ' . $this->name($matches[3])
                )
            );
        }
        if (preg_match('/^[\w-_\s]*[\w-_]+/', $data)) {
            return $this->cacheMethod(__FUNCTION__, $cacheKey, $this->startQuote . $data . $this->endQuote);
        }
        return $this->cacheMethod(__FUNCTION__, $cacheKey, $data);
    }
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column into which this data will be inserted
 * @return string Quoted and escaped data
 */
    public function value($data, $column = null) {
        if (is_array($data) && !empty($data)) {
            return array_map(
                array(&$this, 'value'),
                $data, array_fill(0, count($data), $column)
            );
        }

        if ($data === null || (is_array($data) && empty($data))) {
            return 'NULL';
        }

        if (empty($column)) {
            $column = $this->introspectType($data);
        }
        switch ($column) {
            case 'binary':
                return $this->_connection->quote($data, PDO::PARAM_LOB);
            break;
            case 'boolean':
                return $this->_connection->quote($this->boolean($data, true), PDO::PARAM_BOOL);
            break;
            case 'string':
            case 'text':
                return $this->_connection->quote($data, PDO::PARAM_STR);
            default:
                if ($data === '') {
                    return 'NULL';
                }
                if (is_float($data)) {
                    return str_replace(',', '.', strval($data));
                }
                if ((is_int($data) || $data === '0') || (
                    is_numeric($data) && strpos($data, ',') === false &&
                    $data[0] != '0' && strpos($data, 'e') === false)
                ) {
                    return $data;
                }
                if (strpos($column, 'enum(') !== false) {
                    return $this->_connection->quote($data, PDO::PARAM_STR);
                }
                return $this->_connection->quote($data);
            break;
        }
    }

/**
 * Guesses the data type of an array
 *
 * @param string $value
 * @return void
 */
    public function introspectType($value) {
        if (!is_array($value)) {
            if (is_bool($value)) {
                return 'boolean';
            }
            if (is_float($value) && floatval($value) === $value) {
                return 'float';
            }
            if (is_int($value) && intval($value) === $value) {
                return 'integer';
            }
            if (is_string($value) && strlen($value) > 255) {
                return 'text';
            }
            return 'string';
        }

        $isAllFloat = $isAllInt = true;
        $containsFloat = $containsInt = $containsString = false;
        foreach ($value as $key => $valElement) {
            $valElement = trim($valElement);
            if (!is_float($valElement) && !preg_match('/^[\d]+\.[\d]+$/', $valElement)) {
                $isAllFloat = false;
            } else {
                $containsFloat = true;
                continue;
            }
            if (!is_int($valElement) && !preg_match('/^[\d]+$/', $valElement)) {
                $isAllInt = false;
            } else {
                $containsInt = true;
                continue;
            }
            $containsString = true;
        }

        if ($isAllFloat) {
            return 'float';
        }
        if ($isAllInt) {
            return 'integer';
        }

        if ($containsInt && !$containsString) {
            return 'integer';
        }
        return 'string';
    }
    

/**
 * Queries the database with given SQL statement, and obtains some metadata about the result
 * (rows affected, timing, any errors, number of rows in resultset). The query is also logged.
 * If Configure::read('debug') is set, the log is shown all the time, else it is only shown on errors.
 *
 * ### Options
 *
 * - log - Whether or not the query should be logged to the memory log.
 *
 * @param string $sql
 * @param array $options
 * @param array $params values to be bided to the query
 * @return mixed Resource or object representing the result set, or false on failure
 */
    public function execute($sql, $params = array()) {
        $t = microtime(true);
        $this->_result = $this->_execute($sql, $params);
        if ($this->fullDebug) {
            $this->took = round((microtime(true) - $t) * 1000, 0);
            $this->numRows = $this->affected = $this->lastAffected();
            $this->logQuery($sql);
        }

        return $this->_result;
    }

/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @param array $params list of params to be bound to query
 * @param array $prepareOptions Options to be used in the prepare statement
 * @return mixed PDOStatement if query executes with no problem, true as the result of a successful, false on error
 * query returning no rows, suchs as a CREATE statement, false otherwise
 */
    protected function _execute($sql, $params = array(), $prepareOptions = array()) {    	
        try {
            $this->_lastErr = false;
            $query = $this->_connection->prepare($sql, $prepareOptions);
     /*
     * FETCH_ASSOC = 返回一个以列名为下标的记录集数组
     * FETCH_BOTH  = 返回一个同时以[列名和列编号]为下标的记录集数组
     * FETCH_BOUND = 返回TRUE和把结果集中列的值分配到PHP变量，他们以PDOStatement::bindParam()方法约束
     * FETCH_LAZY  = 结合PDO_FETCH_BOTH和PDO_FETCH_OBJ, 创建允许他们访问的对象变量
     * FETCH_OBJ   = 返回一个匿名对象, 属性名称对应的列名称
     * FETCH_NUM   = 返回一个以列编号为下标的记录集数组, 下标从0开始
     */
            $query->setFetchMode(PDO::FETCH_ASSOC);
            if (!$query->execute($params)) {
                $this->_results = $query;
                $query->closeCursor();
                return false;
            }
            if (!$query->columnCount()) {
                $query->closeCursor();
                if (!$query->rowCount()) {
                    return true;
                }
            }
            return $query;
        } catch (PDOException $e) {
            $this->_setLastError($query);
            if (isset($query->queryString)) {
                $e->queryString = $query->queryString;
            } else {
                $e->queryString = $sql;
            }
           	if (config('debug')) throw $e;
        }
    }

/**
 * Returns a formatted error message from previous database operation.
 *
 * @param PDOStatement $query the query to extract the error from if any
 * @return string Error message with error number
 */
    public function lastError() {
        return $this->_lastErr;
    }
    
    protected function _setLastError($query = null) {
        if ($query instanceof PDOStatement) {
            $error = $query->errorInfo();
        } else {
            $error = $this->_connection->errorInfo();
        }
        if (empty($error[2])) {
            return;
        }
        $this->_lastErr = array($error[1], $error[2]);
    }

/**
 * Returns number of affected rows in previous database operation. If no previous operation exists,
 * this returns false.
 *
 * @param mixed $source
 * @return integer Number of affected rows
 */
    public function lastAffected() {
        if ($this->hasResult()) {
            return $this->_result->rowCount();
        }
        return false;
    }


/**
 * Returns an array of all result rows for a given SQL query.
 * Returns false if no rows matched.
 * @param string $sql SQL statement to be executed
 * @param array $params parameters to be bound as values for the SQL statement
  * @return array Array of resultset rows, or false if no rows matched
 */
    public function query($sql, $params = array()) {
        if ($result = $this->execute($sql, $params)) {
            if ($this->hasResult())
                return $result->fetchAll();
        }
        return false;
    }



/**
 * Checks if the result is valid
 *
 * @return boolean True if the result is valid else false
 */
    public function hasResult() {
        return is_a($this->_result, 'PDOStatement');
    }

/**
 * Get the query log as an array.
 *
 * @param boolean $sorted Get the queries sorted by time taken, defaults to false.
 * @param boolean $clear If True the existing log will cleared.
 * @return array Array of queries run as an array
 */
    public function getLog($sorted = false, $clear = true) {
        if ($sorted) {
            $log = sortByKey($this->_queriesLog, 'took', 'desc', SORT_NUMERIC);
        } else {
            $log = $this->_queriesLog;
        }
        if ($clear) {
            $this->_queriesLog = array();
        }
        return array('log' => $log, 'count' => $this->_queriesCnt, 'time' => $this->_queriesTime);
    }

    public function showLog($sorted = false) {
        $log = $this->getLog($sorted, false);
        if (empty($log['log'])) {
            return;
        }
        if (PHP_SAPI != 'cli') {
            $controller = null;
            $View = new View($controller);
            $View->set('logs', array($this->configKeyName => $log));
            echo $View->element('sqldump');
        } else {
            foreach ($log['log'] as $k => $i) {
                print (($k + 1) . ". {$i['query']}\n");
            }
        }
    }
/**
 * Log given SQL query.
 *
 * @param string $sql SQL statement
 * @return void
 */
    public function logQuery($sql) {
        $this->_queriesCnt++;
        $this->_queriesTime += $this->took;
        $this->_queriesLog[] = array(
            'query'     => $sql,
            'affected'  => $this->affected,
            'numRows'   => $this->numRows,
            'took'      => $this->took
        );
        if (count($this->_queriesLog) > $this->_queriesLogMax) {
            array_pop($this->_queriesLog);
        }
    }

/**
 * Gets full table name including prefix
 *
 * @param mixed $model Either a Model object or a string table name.
 * @param boolean $quote Whether you want the table name quoted.
 * @return string Full quoted table name
 */
    public function fullTableName($model, $quote = false) {
        if (is_object($model)) {
            $table = $model->tablePrefix . $model->useTable;
        } elseif (isset($this->config['prefix'])) {
            $table = $this->config['prefix'] . strval($model);
        } else {
            $table = strval($model);
        }
        if ($quote) {
            return $this->name($table);
        }
        return $table;
    }

/**
 * The "C" in CRUD
 *
 * Creates new records in the database.
 *
 * @param Model $model Model object that the record is for.
 * @param array $fields An array of field names to insert. If null, $model->$data will be
 *   used to generate field names.
 * @param array $values An array of values with keys matching the fields. If null, $model->$data will
 *   be used to generate values.
 * @return boolean Success
 */
    public function create( $model, $data) {
        $fields = $values = array();
        foreach($data as $key => $value) {
            $fields[] = $key;
            $values[] = $this->value($value, $model->getColumnType($key));
        }
        $query = array(
            'table' => $this->fullTableName($model->useTable),
            'fields' => implode(', ', $fields),
            'values' => implode(', ', $values)
        );

        if ($this->execute($this->renderStatement('create', $query))) {
            $id = $this->lastInsertId();        
            $model->lastInsertID($id);
            return true;
        }

        $model->onError();
        return false;
    }
    
    //全部使用sql param的版本，未测试
    public function create1(Model $model, $fields, $values) {
        if (!is_array($fields) || !is_array($values))
            return false;
        if (is_string($fields))
            $fields = explode(', ', $fields);
        if (array_count($fields) != array_count($values))
            return false;
            
        $marks = array_pad(array(), array_count($fields), '?');
        $query = array(
            'table' => $this->fullTableName($model),
            'fields' => implode(', ', $fields),
            'values' => implode(', ', $marks)
        );

        if ($this->execute($this->renderStatement('create', $query), $values)) {
            $id = $this->lastInsertId();
            $model->lastInsertID($id);
            return true;
        }
        $model->onError();
        return false;
    }

/**
 * Generates and executes an SQL UPDATE statement for given model, fields, and values.
 * For databases that do not support aliases in UPDATE queries.
 *
 * @param Model $model
 * @param array $fields
 * @param array $values
 * @param mixed $conditions
 * @return boolean Success
 */
    public function update($model, $data, $conditions, $params = null) {
        $fields = implode(', ', $this->_prepareUpdateFields($model, $data));
		
        $alias = $joins = null;
        $table = $this->fullTableName($model->useTable);
        $conditions = $this->conditions($conditions);
        
        $query = compact('table', 'alias', 'joins', 'fields', 'conditions');

        if (!$this->execute($this->renderStatement('update', $query), $params)) {
            $model->onError();
            return false;
        }
        return true;
    }
    
    //全部使用sql param的版本，未测试
    public function update1(Model $model, $fields, $values, $conditions, $params = null) {
        if (!is_array($fields) || !is_array($values))
            return false;
        if (array_count($fields) != array_count($values))
            return false;
            
        $updates = array();
        foreach ($fields as $field) {
            $updates[] = $field . ' = ?';
        }
        $fields = implode(', ', $updates);
        if (isset($params)) {
            foreach($params as $param) {
                $values[] = $param;
            }
        }

        $alias = $joins = null;
        $table = $this->fullTableName($model);
        $conditions = $this->conditions($conditions);

        $query = compact('table', 'alias', 'joins', 'fields', 'conditions');

        if (!$this->execute($this->renderStatement('update', $query), $values)) {
            $model->onError();
            return false;
        }
        return true;
    }
/**
 * Quotes and prepares fields and values for an SQL UPDATE statement
 *
 * @param $model
 * @param array $fields
 * @param boolean $quoteValues If values should be quoted, or treated as SQL snippets
 * @param boolean $alias Include the model alias in the field name
 * @return array Fields and values, quoted and preparted
 */
    protected function _prepareUpdateFields($model, $fields) {
        $updates = array();
        foreach ($fields as $field => $value) {
            if ($value === null) {
                $updates[] = $field . ' = NULL';
                continue;
            }
            if (is_int($field)) {
                $updates[] = $value;
                continue;
            }
            $update = $field . ' = ';

            $updates[] =  $update . $this->value($value, $model->getColumnType($field));
        }
        return $updates;
    }
    

/**
 * Generates and executes an SQL DELETE statement.
 * For databases that do not support aliases in UPDATE queries.
 *
 * @param Model $model
 * @param mixed $conditions
 * @return boolean Success
 */
    public function delete($model, $conditions, $params) {
        $alias = $joins = null;
        $table = $this->fullTableName($model);

        if ($conditions === false) {
            return false;
        }
        $conditions = $this->conditions($conditions);
        $result = $this->execute($this->renderStatement('delete', compact('alias', 'table', 'joins', 'conditions')), $params);
        return $result;
    }
    
    
/**
 * The "R" in CRUD
 *
 * Reads record(s) from the database.
 *
 * @param string $model A Model name that the query is for.
 * @param array $queryData An array of queryData information containing keys similar to Model::find()
 * @param integer $recursive Number of levels of association
 * @return mixed boolean false on error/failure.  An array of results on success.
 */
    public function read($model, $queryData = array(), $params = null) {
        if (is_string($queryData)) {
            $query = $queryData;
        } else {
            $queryData = $this->_scrubQueryData($queryData);
            $modelAlias = $model->alias;
            if (!empty($queryData['alias'])) {
            	$modelAlias = $queryData['alias'];
            }
            $modelTable = $model->useTable;
        
            if (empty($queryData['fields'])) {
                $queryData['fields'] = array_keys($model->schema());
            }
            if (!empty($queryData['limit']) && !empty($queryData['page'])) {
                $page = $queryData['page'];
                if ($page < 1) $page = 1;
                $queryData['offset'] = ($page - 1) * $queryData['limit'];
            }
        
            $query = $this->buildStatement(
                array(
                    'fields' => $queryData['fields'],
                    'table' => $this->fullTableName($modelTable),
                    'alias' => $modelAlias,
                    'limit' => $queryData['limit'],
                    'offset' => $queryData['offset'],
                    'joins' => $queryData['joins'],
                    'conditions' => $queryData['conditions'],
                    'order' => $queryData['order'],
                    'group' => $queryData['group'],
                	'having' => $queryData['having']
                ),
                $model
            );
        }
        $resultSet = $this->query($query, $params);
        if ($resultSet === false)
            $model->onError();
        return $resultSet;

    }
/**
 * Builds and generates a JOIN statement from an array.  Handles final clean-up before conversion.
 *
 * @param array $join An array defining a JOIN statement in a query
 * @return string An SQL JOIN statement to be used in a query
 * @see DboSource::renderJoinStatement()
 * @see DboSource::buildStatement()
 */
    public function buildJoinStatement($join) {
        $data = array_merge(array(
            'type' => null,
            'alias' => null,
            'table' => 'join_table',
            'conditions' => array()
        ), $join);

        if (!empty($data['alias'])) {
            $data['alias'] = $this->alias . $data['alias'];
        }
        if (!empty($data['conditions'])) {
            $data['conditions'] = $this->conditions($data['conditions'], false);
        }
        if (!empty($data['table'])) {
            $data['table'] = $this->fullTableName($data['table']);
        }
        
        extract($data);
        return trim("{$type} JOIN {$table} {$alias} ON ({$conditions})");
    }

/**
 * Builds and generates an SQL statement from an array.  Handles final clean-up before conversion.
 *
 * @param array $query An array defining an SQL query
 * @param Model $model The model object which initiated the query
 * @return string An executable SQL statement
 * @see DboSource::renderStatement()
 */
    public function buildStatement($query, $model) {
        $query = array_merge(array('offset' => null, 'joins' => array()), $query);
        if (!empty($query['joins']) && !is_string($query['joins'])) {
            $count = count($query['joins']);
            for ($i = 0; $i < $count; $i++) {
                if (is_array($query['joins'][$i])) {
                    $query['joins'][$i] = $this->buildJoinStatement($query['joins'][$i]);
                }
            }
        }

        return $this->renderStatement('select', array(
            'conditions' => $this->conditions($query['conditions'], true),
            'fields' => implode(', ', $query['fields']),
            'table' => $query['table'],
            'alias' => $this->alias . $query['alias'],
            'order' => $this->order($query['order']),
            'limit' => $this->limit($query['limit'], $query['offset']),
            'joins' => implode(' ', $query['joins']),
            'group' => $this->group($query['group'], $model),
        	'having' => $this->having($query['having'])
        ));
    }

/**
 * Renders a final SQL statement by putting together the component parts in the correct order
 *
 * @param string $type type of query being run.  e.g select, create, update, delete, schema, alter.
 * @param array $data Array of data to insert into the query.
 * @return string Rendered SQL expression to be run.
 */
    public function renderStatement($type, $data) {
        extract($data);
        $aliases = null;
        switch (strtolower($type)) {
            case 'select':
                return "SELECT {$fields} FROM {$table} {$alias} {$joins} {$conditions} {$group} {$having} {$order} {$limit}";
            case 'create':
                return "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
            case 'update':
                if (!empty($alias)) {
                    $aliases = "{$this->alias}{$alias} {$joins} ";
                }
                return "UPDATE {$table} {$aliases}SET {$fields} {$conditions}";
            case 'delete':
                if (!empty($alias)) {
                    $aliases = "{$this->alias}{$alias} {$joins} ";
                }
                return "DELETE {$alias} FROM {$table} {$aliases}{$conditions}";
            case 'schema':
                foreach (array('columns', 'indexes', 'tableParameters') as $var) {
                    if (is_array(${$var})) {
                        ${$var} = "\t" . join(",\n\t", array_filter(${$var}));
                    } else {
                        ${$var} = '';
                    }
                }
                if (trim($indexes) !== '') {
                    $columns .= ',';
                }
                return "CREATE TABLE IF NOT EXISTS {$table} (\n{$columns}\n{$indexes})\n{$tableParameters};";
            case 'alter':
                return; //alter table
        }
    }


    

/**
 * Begin a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
    public function begin() {
        if ($this->_transactionStarted || $this->_connection->beginTransaction()) {
            $this->_transactionStarted = true;
            $this->_transactionNesting++;
            return true;
        }
        return false;
    }

/**
 * Commit a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
    public function commit() {
        if ($this->_transactionStarted) {
            $this->_transactionNesting--;
            if ($this->_transactionNesting <= 0) {
                $this->_transactionStarted = false;
                $this->_transactionNesting = 0;
                return $this->_connection->commit();
            }
            return true;
        }
        return false;
    }

/**
 * Rollback a transaction
 *
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
    public function rollback() {
        if ($this->_transactionStarted && $this->_connection->rollBack()) {
            $this->_transactionStarted = false;
            $this->_transactionNesting = 0;
            return true;
        }
        return false;
    }

/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @param mixed $source
 * @return mixed
 */
    public function lastInsertId($source = null) {
        return $this->_connection->lastInsertId();
    }


/**
 * Private helper method to remove query metadata in given data array.
 *
 * @param array $data
 * @return array
 */
    protected function _scrubQueryData($data) {
        static $base = null;
        if ($base === null) {
            $base = array_fill_keys(array('conditions', 'fields', 'joins', 'order', 'limit', 'offset', 'group', 'having'), array());
        }
        return (array)$data + $base;
    }

    //create WHERE clause in sql
    public function conditions($conditions, $where = true) {
        $clause = $out = '';
        if ($where) {
            $clause = ' WHERE ';
        }

        if (is_bool($conditions)) {
            return $clause . (int)$conditions . ' = 1';
        }
        if (!empty($conditions))
            return $clause . $conditions;
        else
            return $clause . '1 = 1';
    }

    //create LIMIT clause in sql
    public function limit($limit, $offset = null) {
        if ($limit) {
            $rt = ' LIMIT';
            if ($offset) {
                $rt .= ' ' . $offset . ',';
            }
            $rt .= ' ' . $limit;
            return $rt;
        }
        return null;
    }


    //create ORDER BY clause in sql
    public function order($keys) {
        if ($keys) {
            return ' ORDER BY ' . $keys;
        }
        return null;
    }


    //create GROUP BY clause in sql
    public function group($group) {
        if ($group) {
            return ' GROUP BY ' . $group;
        }
        return null;
    }

    //create HAVING clause in sql
    public function having($having) {
        if ($having) {
            return ' HAVING ' . $having;
        }
        return null;
    }
    
    public function close() {
        $this->disconnect();
    }


/**
 * Translates between PHP boolean values and Database (faked) boolean values
 *
 * @param mixed $data Value to be translated
 * @param boolean $quote
 * @return string|boolean Converted boolean value
 */
    public function boolean($data, $quote = false) {
        if ($quote) {
            return !empty($data) ? '1' : '0';
        }
        return !empty($data);
    }


/**
 * Returns an array of the indexes in given datasource name.
 *
 * @param string $model Name of model to inspect
 * @return array Fields in table. Keys are column and unique
 */
    public function index($model) {
        $index = array();
        $table = $this->fullTableName($model);
        $old = version_compare($this->getVersion(), '4.1', '<=');
        if ($table) {
            $indices = $this->_execute('SHOW INDEX FROM ' . $table);
            while ($idx = $indices->fetch()) {
                if ($old) {
                    $idx = (object) current((array)$idx);
                }
                if (!isset($index[$idx->Key_name]['column'])) {
                    $col = array();
                    $index[$idx->Key_name]['column'] = $idx->Column_name;
                    $index[$idx->Key_name]['unique'] = intval($idx->Non_unique == 0);
                } else {
                    if (!empty($index[$idx->Key_name]['column']) && !is_array($index[$idx->Key_name]['column'])) {
                        $col[] = $index[$idx->Key_name]['column'];
                    }
                    $col[] = $idx->Column_name;
                    $index[$idx->Key_name]['column'] = $col;
                }
            }
            $indices->closeCursor();
        }
        return $index;
    }

    
/**
 * Generate a database-native schema for the given Schema object
 *
 * @param Model $schema An instance of a subclass of CakeSchema
 * @param string $tableName Optional.  If specified only the table name given will be generated.
 *   Otherwise, all tables defined in the schema are generated.
 * @return string
 */
    public function createSchema($tableName, $columns) {
        $cols = $colList = $indexes = $tableParameters = array();
        $primary = null;
        $table = $this->fullTableName($tableName);

        foreach ($columns as $name => $col) {
            if (is_string($col)) {
                $col = array('type' => $col);
            }
            if (isset($col['key']) && $col['key'] === 'primary') {
                $primary = $name;
            }
            if ($name !== 'indexes' && $name !== 'tableParameters') {
                $col['name'] = $name;
                if (!isset($col['type'])) {
                    $col['type'] = 'string';
                }
                $cols[] = $this->buildColumn($col);
            } elseif ($name === 'indexes') {
                $indexes = array_merge($indexes, $this->buildIndex($col));
            } elseif ($name === 'tableParameters') {
                $tableParameters = array_merge($tableParameters, $this->buildTableParameters($col));
            }
        }
        if (empty($indexes) && !empty($primary)) {
            $col = array('PRIMARY' => array('column' => $primary, 'unique' => 1));
            $indexes = array_merge($indexes, $this->buildIndex($col));
        }
        $columns = $cols;
        return $this->renderStatement('schema', compact('table', 'columns', 'indexes', 'tableParameters')) . "\n\n";
    }
    
/**
 * Generate a "drop table" statement for the given Schema object
 *
 * @param CakeSchema $schema An instance of a subclass of CakeSchema
 * @param string $table Optional.  If specified only the table name given will be generated.
 *   Otherwise, all tables defined in the schema are generated.
 * @return string
 */
    public function dropSchema($tableName) {
        $out = 'DROP TABLE IF EXISTS ' . $this->fullTableName($tableName) . ";\n";
        return $out;
    }
    
    
/**
 * Format parameters for create table
 *
 * @param array $parameters
 * @param string $table
 * @return array
 */
    public function buildTableParameters($parameters) {
        $result = array();
        foreach ($parameters as $name => $value) {
            if (isset($this->tableParameters[$name])) {
                if ($this->tableParameters[$name]['quote']) {
                    $value = $this->value($value);
                }
                $result[] = $this->tableParameters[$name]['value'] . $this->tableParameters[$name]['join'] . $value;
            }
        }
        return $result;
    }
    
    
    
/**
 * Build the field parameters, in a position
 *
 * @param string $columnString The partially built column string
 * @param array $columnData The array of column data.
 * @param string $position The position type to use. 'beforeDefault' or 'afterDefault' are common
 * @return string a built column with the field parameters added.
 */
    protected function _buildFieldParameters($columnString, $columnData, $position) {
        foreach ($this->fieldParameters as $paramName => $value) {
            if (isset($columnData[$paramName]) && $value['position'] == $position) {
                if (isset($value['options']) && !in_array($columnData[$paramName], $value['options'])) {
                    continue;
                }
                $val = $columnData[$paramName];
                if ($value['quote']) {
                    $val = $this->value($val);
                }
                $columnString .= ' ' . $value['value'] . $value['join'] . $val;
            }
        }
        return $columnString;
    }
    
/**
 * Generate a database-native column schema string
 *
 * @param array $column An array structured like the following: array('name'=>'value', 'type'=>'value'[, options]),
 *   where options can be 'default', 'length', or 'key'.
 * @return string
 */
    public function buildColumn($column) {
        $name = $type = null;
        extract(array_merge(array('null' => true), $column));

        if (empty($name) || empty($type)) {
            trigger_error(__('Column name or type not defined in schema'), E_USER_WARNING);
            return null;
        }

        if (!isset($this->columns[$type])) {
            trigger_error(__('Column type %s does not exist', $type), E_USER_WARNING);
            return null;
        }

        $real = $this->columns[$type];
        $out = $this->startQuote.$name.$this->endQuote . ' ' . $real['name'];

        if (isset($column['length'])) {
            $length = $column['length'];
        } elseif (isset($column['limit'])) {
            $length = $column['limit'];
        } elseif (isset($real['length'])) {
            $length = $real['length'];
        } elseif (isset($real['limit'])) {
            $length = $real['limit'];
        }
        if (isset($length)) {
            $out .= '(' . $length . ')';
        }

        if (($column['type'] === 'integer' || $column['type'] === 'float' ) && isset($column['default']) && $column['default'] === '') {
            $column['default'] = null;
        }
        $out = $this->_buildFieldParameters($out, $column, 'beforeDefault');

        if (isset($column['key']) && $column['key'] === 'primary' && $type === 'integer') {
            $out .= ' ' . $this->columns['primary_key']['name'];
        } elseif (isset($column['key']) && $column['key'] === 'primary') {
            $out .= ' NOT NULL';
        } elseif (isset($column['default']) && isset($column['null']) && $column['null'] === false) {
            $out .= ' DEFAULT ' . $this->value($column['default'], $type) . ' NOT NULL';
        } elseif (isset($column['default'])) {
            $out .= ' DEFAULT ' . $this->value($column['default'], $type);
        } elseif ($type !== 'timestamp' && !empty($column['null'])) {
            $out .= ' DEFAULT NULL';
        } elseif ($type === 'timestamp' && !empty($column['null'])) {
            $out .= ' NULL';
        } elseif (isset($column['null']) && $column['null'] === false) {
            $out .= ' NOT NULL';
        }
        if ($type === 'timestamp' && isset($column['default']) && strtolower($column['default']) === 'current_timestamp') {
            $out = str_replace(array("'CURRENT_TIMESTAMP'", "'current_timestamp'"), 'CURRENT_TIMESTAMP', $out);
        }
        return $this->_buildFieldParameters($out, $column, 'afterDefault');
    }
    
    public function buildIndex($indexes) {
        $join = array();
        foreach ($indexes as $name => $value) {
            $out = '';
            if ($name === 'PRIMARY') {
                $out .= 'PRIMARY ';
                $name = null;
            } else {
                if (!empty($value['unique'])) {
                    $out .= 'UNIQUE ';
                }
                $name = $this->startQuote . $name . $this->endQuote;
            }
            if (is_array($value['column'])) {
                $out .= 'KEY ' . $name . ' (' . implode($this->endQuote . ', ' . $this->startQuote, $value['column']) . ')';
            } else {
                $out .= 'KEY ' . $name . ' (' . $this->startQuote . $value['column'] . $this->endQuote . ')';
            }
            $join[] = $out;
        }
        return $join;
    }
    

    
    public function cacheMethod($method, $key, $value = null) {
        if ($this->cacheMethods === false) {
            return $value;
        }
        if (empty(self::$methodCache)) {
            self::$methodCache = Cache::read('method_cache', '_cache_core_');
        }
        if ($value === null) {
            return (isset(self::$methodCache[$method][$key])) ? self::$methodCache[$method][$key] : null;
        }
        $this->_methodCacheChange = true;
        return self::$methodCache[$method][$key] = $value;
    }
    
    public function flushMethodCache() {
        $this->_methodCacheChange = true;
        self::$methodCache = array();
    }
    
/**
 * Used for storing in cache the results of the in-memory methodCache
 *
 */
    public function __destruct() {
        if ($this->_methodCacheChange) {
            Cache::write('method_cache', self::$methodCache, null, '_cache_core_');
        }
    }

}
