<?php
class Table {
	public $useDbConfig = 'default';            	//使用的数据库的配置名
	public $useTable = false;                   	//是否使用数据库的表，如使用则值为表名
	public $name = null;							//模型名称
	public $alias = null;                       	//模型的别名
	public $tablePrefix = null;	
	public $primaryKey = null;
	
	public $cacheEnabled = false;
	
	public $_schema = null;                  //模型schema的本地缓存，只存在于当前请求的生命周期中
	public $_lastInsertID = null;            //最后一次插入操作返回的ID键值
	protected $_sourceConfigured = false;       //标记本模型的数据源是否已经初始化
	protected $_modelClassName = null;
	
	protected static $_models = array();
	
	public static function load($model_class_name)
	{
		if (!isset(self::$_models[$model_class_name]))
		{
			self::$_models[$model_class_name] = new Table($model_class_name);
		}
	
		return self::$_models[$model_class_name];
	}
	
	public function __construct($className) {
		$this->_modelClassName = $className;
		$this->useDbConfig = $className::$useDbConfig;
		$this->useTable = $className::$useTable;
		$this->name = strtolower($className);
		$this->alias = strtolower($className);
		$this->primaryKey = $this->_getPrimaryKey();
	}
	
	/**
	 * 获取符合条件的记录数.
	 *
	 * <code>
	 * YourModel::count('amount > 3.14159265');
	 * </code>
	 *
	 * @param string where语句中的条件
	 * @param array 当$conditions里面带参数时，指定各参数值的数组
	 * @return int
	 */
	public function count($conditions='1=1', array $params = array()) {
		$db = $this->getDataSource();
		$table = $db->fullTableName($this->useTable);
		$results = $db->query("select count(*) as c from $table where $conditions", $params);
		return $results[0]['c'];
	}
	
	/**
	 * 总计
	 * @param string $field
	 * @param string $conditions
	 * @param array $params
	 */
	public function sum($field, $conditions='1=1', array $params = array()) {
		$db = $this->getDataSource();
		$table = $db->fullTableName($this->useTable);
		$results = $db->query("select sum($field) as c from $table where $conditions", $params);
		return $results[0]['c'];
	}
	
	/**
	 * 
	 * @param array $attrs 更新字段数值
	 * @param array $conditions 
	 * @param array $params
	 */
	public function update_all($attrs, $conditions, $params)
	{
		$db = $this->getDataSource();
		return $db->update($this, $attrs, $conditions, $params);
	}
	
	public function delete_all($conditions, $params=array())
	{
		$db = $this->getDataSource();
		$db->delete($this, $conditions, $params);
		return $db->lastAffected();
	}
	
	/**
	 * 查询是否存在符合条件的记录.
	 *
	 * <code>
	 * YourModel::exsit('amount > 3.14159265');
	 * </code>
	 *
	 * @param string where语句中的条件
	 * @param array 当$conditions里面带参数时，指定各参数值的数组
	 * @return bool
	 */
	public function exsit($conditions, array $params = array()) {
		return $this->count($conditions, $params) > 0 ? true : false;
	}
	
	/**
	 * 查找全部记录
	 * (参数和返回和Model::find相同)
	 * @return array|Model 返回一个或者多个Model实例
	 * @see Model::find
	 */
	public function all(array $query = array(),array $params = array()) {
		return $this->_find('all', $query, $params);
	}
	
	/**
	 * 查找第一条记录
	 * @return Model 返回一个Model实例
	 * @see Model::find
	 */
	public function first(array $query = array(), array $params = array()) {
		return $this->_find('first', $query, $params);
	}
	
	/**
	 * 查找最后一条记录
	 * @return Model 返回一个Model实例
	 * @see Model::find
	 */
	public function last(array $query = array(), array $params = array()) {
		return $this->_find('last', $query, $params);
	}
	
	/**
	 * 根据主键查询
	 * @param mixed $pk 主键或主键数组
	 * @return array|Model 返回一个或者多个Model实例
	 */
	public function findByPk($values) {
		$db = $this->getDataSource();
		$pk = $this->_getPrimaryKey();
	
		if (is_array($values)) {
			if(!empty($values)) {
				$rows = $db->read($this, array('conditions'=>"$pk in (".implode(',', $values).")"));
				$result = array();
				foreach ($rows as $row) {
					$result[] = new $this->_modelClassName($row, false, true, false);
				}
				return $result;
			}
			else
				return array();
		}
		else { // 单 pk
			$row = false;
			if ($this->cacheEnabled) {
				$row = Cache::read('model:'.$this->name.':'.$values);
			}
				
			if(!$row) {
				$rows = $db->read($this, array('conditions'=>"$pk = ?"), array($values));
				if($rows && count($rows)>0) {
					$row = $rows[0];
					if ($this->cacheEnabled) {
						Cache::write('model:'.$this->name.':'.$values, $row);
					}
				}
			}

			if($row)
				return new $this->_modelClassName($row, false, true, false);
			else
				return null;			
		}
	}
	
	protected function beforeFind($values) {}
	protected function afterFind($values) {}
	
	/**
	 * 查找记录
	 * @param array $query 指定查询语句中各组的内容
	 * @param array $params 当$query里面的 conditions带参数时，指定各参数值的数组
	 * @return array|Model 返回一个或者多个Model实例
	 * <code>
	 * $query = array(
	 * 		'fields' => array('accountid', 'nickname'),
	 * 		'conditions' => 'accountid = ?', 	// where语句的字符串，可以带参数
	 * 		'page' => 1,                    	// 页号
	 * 		'limit' => 20,                  	// 返回的最大记录数
	 * 		'order' => 'created desc'       	// order by语句的字符串
	 * 		'joins' =>                    		// join语句的数组，为一个数组，每一个join项是数组中得一项
	 * )
	 *
	 * $profile = UserProfile::find($query, array(1));
	 * </code>
	 */
	public function find($query = array(), $params = array()) {
		return $this->_find('all', $query, $params);
	}
	
	public function _find($type, $query = array(), $params = array()) {
		$single = true;
		switch ($type) {
			case 'all':
				$single = false;
				break;
			case 'last':
				if (!array_key_exists('order',$query))
					$query['order'] = $this->_getPrimaryKey() . ' DESC';
				else
					$query['order'] = $this->reverse_order($query['order']);
				// fall thru
			case 'first':
				$query['limit'] = 1;
				$query['page'] = 1;
				break;
			case 'firstLock':
				$query['conditions'] .= " FOR UPDATE";
				break;
		}
	
		$readonly = array_key_exists('fields', $query);
	
		$rows = $this->getDataSource()->read($this, $query, $params);
		if($rows === false) return false;
		$result = array();
		foreach ($rows as $row) {
			$obj = new $this->_modelClassName($row, false, true, false);
			//$obj->__readonly = $readonly;
			$result[] = $obj;
		}
		if($single)
			return empty($result) ? null : $result[0];
		return $result;
	}
	
	/**
	 * TODO: 最好放在其他地方
	 * Reverses an order clause.
	 */
	private function reverse_order($order)
	{
		if (!trim($order))
			return $order;
	
		$parts = explode(',',$order);
	
		for ($i=0,$n=count($parts); $i<$n; ++$i)
		{
			$v = strtolower($parts[$i]);
	
			if (strpos($v,' asc') !== false)
				$parts[$i] = preg_replace('/asc/i','DESC',$parts[$i]);
			elseif (strpos($v,' desc') !== false)
			$parts[$i] = preg_replace('/desc/i','ASC',$parts[$i]);
			else
				$parts[$i] .= ' DESC';
		}
		return join(',',$parts);
	}
	
	
	/**
	 * 执行查询
	 * @param array $query
	 * @param array $params
	 * @return array 查询失败返回false，否则返回结果数组
	 */
	public function query($query = array(), $params = array()) {
		return $this->getDataSource()->read($this, $query, $params);
	}
	
	/**
	 * 用sql语句进行查询
	 * @param string $sql
	 * @return array 查询失败返回false，否则返回结果数组
	 */
	public function queryBySql($sql, array $params=array()) {
		return $this->getDataSource()->query($sql, $params);
	}
	
	/**
	 * 创建模型并保持至数据库
	 *
	 * @param array $attributes 属性数组
	 * @param boolean $validate 是否验证数据
	 * @return Model
	 */
	public function create($attributes, $validate=true)
	{
		$datetime = date('Y-m-d H:i:s', time());
		if ($this->hasField('created') && !isset($attributes['created']))
			$attributes['created'] = $datetime;
		if ($this->hasField('modified') && !isset($attributes['modified']))
			$attributes['modified'] = $datetime;
	
		$model = new $this->_modelClassName($attributes);
		$model->save($validate);
		return $model;
	}
	
	/**
	 * 获取本模型表的schema
	 * @param string $field 指定获取的字段名，如为false则返回所有字段的定义
	 */
	public function schema($field = false) {
		if (!is_array($this->_schema) || $field === true) {
			
			$db = $this->getDataSource();
			if (method_exists($db, 'describe') && $this->useTable !== false) {
				$this->_schema = $db->describe($this->useTable);
	
			} elseif ($this->useTable === false) {
				$this->_schema = array();
			}
		}
		if (is_string($field)) {
			if (isset($this->_schema[$field])) {
				return $this->_schema[$field];
			} else {
				return null;
			}
		}
		return $this->_schema;
	}
	
	
	/**
	 * 获取本模型表的各字段的类型
	 */
	public function getColumnTypes() {
		$columns = $this->schema();
		if (empty($columns)) {
			trigger_error('(Model::getColumnTypes) Unable to build model field data. If you are using a model without a database table, try implementing schema()', E_USER_WARNING);
		}
		$cols = array();
		foreach ($columns as $field => $values) {
			$cols[$field] = $values['type'];
		}
		return $cols;
	}
	
	/**
	 * 获取本模型表的指定字段的类型
	 * @param string $column 指定获取的字段名
	 */
	public function getColumnType($column) {
		$db = $this->getDataSource();
		$cols = $this->schema();
		$model = null;
	
		$column = str_replace(array($db->startQuote, $db->endQuote), '', $column);
	
		if (strpos($column, '.')) {
			list($model, $column) = explode('.', $column);
		}
		if ($model != $this->alias && isset($this->{$model})) {
			return $this->{$model}->getColumnType($column);
		}
		if (isset($cols[$column]) && isset($cols[$column]['type'])) {
			return $cols[$column]['type'];
		}
		return null;
	}
	
	
	/**
	 * 判断本模型是否包含指定的字段
	 * @param string $name 指定需要检查的字段名
	 */
	public function hasField($name) {
		if (empty($this->_schema)) {
			$this->schema();
		}
		if ($this->_schema != null) {
			return isset($this->_schema[$name]);
		}
		return false;
	}
	
	
	public function _getPrimaryKey() {
		$this->schema();
		foreach ($this->_schema as $key => $value) {
			if (isset($value['key']) && $value['key'] == 'primary') {
				return $key;
			}
		}
		return null;
	}
	
	/**
	 * 获取数据源
	 * @return Mysql
	 */
	public function getDataSource() {
		
		if (!$this->_sourceConfigured && $this->useTable !== false) {
				
			$this->_sourceConfigured = true;
			$this->setDataSource($this->useDbConfig);
		}
		return DBManager::instance()->getDataSource($this->useDbConfig);
	}
	
	/**
	 * 构建数据源
	 * @param string $dataSource
	 * @throws MissingConnectionException
	 */
	public function setDataSource($dataSource = null) {
		$oldConfig = $this->useDbConfig;
	
		if ($dataSource != null) {
			$this->useDbConfig = $dataSource;
		}
		$db = DBManager::instance()->getDataSource($this->useDbConfig);
		if (!empty($oldConfig) && isset($db->config['prefix'])) {
			$oldDb = DBManager::instance()->getDataSource($oldConfig);
	
			if (!isset($this->tablePrefix) || (!isset($oldDb->config['prefix']) || $this->tablePrefix == $oldDb->config['prefix'])) {
				$this->tablePrefix = $db->config['prefix'];
			}
		} elseif (isset($db->config['prefix'])) {
			$this->tablePrefix = $db->config['prefix'];
		}
	
		if (empty($db) || !is_object($db)) {
			throw new MissingConnectionException(array('class' => $this->name));
		}
	}
	
	protected function validates($data) {
		return true;
	}
	
	/**
	 * Error回调函数，在datasource的create、update、read、delete中回调
	 */
	public function onError() {}
	
	public function lastError() {}
	
	public function lastInsertId($id = null) {
		if (isset($id)) {
			$this->_lastInsertID = $id;
		} else {
			return $this->_lastInsertID;
		}
	}
	
	
	public function lastAffected() {
		return $this->getDataSource()->lastAffected();
	}
}