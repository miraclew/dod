<?php
/**
 * 模型的基类
 *
 */
class Model extends Object {
	const BELONGS_TO 	= 'BelongsToRelation';
	const HAS_ONE 		= 'HasOneRelation';
	const HAS_MANY 		= 'HasManyRelation';
	const MANY_MANY 	= 'ManyToManyRelation';
	
	/**
	 * 返回模型的 {@link Table} 对象.
	 *
	 * @return Table
	 */
	public static function table()
	{
		return Table::load(get_called_class());
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
	public static function count($conditions='1=1', array $params = array()) {		
		return static::table()->count($conditions, $params);
	}
	
	/**
	 * 总计
	 * @param string $field 合计字段
	 * @param string $conditions
	 * @param array $params
	 */
	public static function sum($field, $conditions='1=1', array $params = array()) {
		return static::table()->sum($field, $conditions, $params);
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
	public static function exsit($conditions, array $params = array()) {
		return static::count($conditions, $params) > 0 ? true : false;
	}
	
	/**
	 * 查找全部记录 
	 * (参数和返回和Model::find相同)
	 * @return array|Model 返回一个或者多个Model实例
	 * @see Model::find
	 */
	public static function all(array $query = array(),array $params = array()) {
		return static::table()->_find('all', $query, $params);
	}
	
	/**
	 * 查找第一条记录
	 * 
	 * <code>
	 * YourModel::first(array('conditions'=>'accountid=?'), array($accountid));
	 * </code>
	 * 
	 * @return Model 返回一个Model实例
	 * @see Model::find
	 */
	public static function first(array $query = array(), array $params = array()) {
		return static::table()->_find('first', $query, $params);
	}
	
	/**
	 * 查找最后一条记录
	 * <code>
	 * YourModel::last(array('conditions'=>'accountid=?'), array($accountid));
	 * </code>
	 * @return Model 返回一个Model实例
	 * @see Model::find
	 */
	public static function last(array $query = array(), array $params = array()) {
		return static::table()->_find('last', $query, $params);
	}
	
	/**
	 * 根据主键查询
	 * @param mixed $pk 主键或主键数组
	 * @return array|Model 返回一个或者多个Model实例
	 */
	public static function findByPk($id) {
		return static::table()->findByPk($id);		
	}
	
	protected static function beforeFind($values) {}
	protected static function afterFind($values) {}
	
	/**
	 * 查找记录
	 * @param array $query 指定查询语句中各组的内容
	 * @param array $params 当$query里面的 conditions带参数时，指定各参数值的数组
	 * @return array|Model 返回一个或者多个Model实例
	 * 
	 * <code>
	 * $query = array(
	 * 		'fields' => array('accountid', 'nickname'),
	 * 		'conditions' => 'accountid = ?', 	// where语句的字符串，可以带参数
	 * 		'page' => 1,                    	// 页号
	 * 		'limit' => 20,                  	// 返回的最大记录数
	 * 		'order' => 'created desc'       	// order by语句的字符串
	 * 		// join语句的数组，为一个数组，每一个join项是数组中得一项
	 * 		'joins' => array(array('type'  => 'left','alias' => 'r','table' => 'qyh_room.rooms','conditions' => 'r.id = favorite.objectid')) 
	 * )
	 * 
	 * $profile = UserProfile::find($query, array(1));
	 * </code>
	 * 
	 */
	public static function find($query = array(), $params = array()) {
		return static::table()->_find('all', $query, $params);
	}
	
	/**
	 * 执行查询
	 * @param array $query
	 * @param array $params
	 * @return array 查询失败返回false，否则返回结果数组
	 */
	public static function query($query = array(), $params = array()) {
		return static::table()->query($query, $params);
	}
	
	/**
	 * 用sql语句进行查询
	 * @param string $sql
	 * @return array 查询失败返回false，否则返回结果数组
	 */
	public static function queryBySql($sql, array $params=array()) {
		return static::table()->queryBySql($sql, $params);
	}
	
	/**
	 * 创建模型并保持至数据库
	 *
	 * @param array $attributes 属性数组
	 * @param boolean $validate 是否验证数据
	 * @return Model
	 */
	public static function create($attributes, $validate=true)
	{
		$class_name = get_called_class();
		
		$datetime = date('Y-m-d H:i:s', time());
		if (static::table()->hasField('created') && !isset($attributes['created']))
			$attributes['created'] = $datetime;
		if (static::table()->hasField('modified') && !isset($attributes['modified']))
			$attributes['modified'] = $datetime;
		
		$model = new $class_name($attributes);
		$model->save($validate);
		return $model;
	}
	
	/**
	 * 删除符合条件的记录
	 *
	 * <code>
	 * YourModel::delete_all('name = "Tito" and age > ?', array(30));
	 * </code>
	 * @param string $conditions
	 * @param array $options
	 * @return integer 被影响的记录数, 未有被影响记录返回false 
	 */
	public static function delete_all($conditions, $params=array())
	{
		$table = static::table();
		return $table->delete_all($conditions, $params);
	}
	
	/**
	 * 更新符合条件的记录
	 *
	 * <code>
	 * YourModel::update_all(array('age'=> 20), "name = '?'", array('TT'));
	 * </code>
	 * 
	 * @param array $attrs 更新的字段数组
	 * @param string $conditions
	 * @param array $params conditions中的?参数
	 * @return bool 是否成功
	 */
	public static function update_all($attrs, $conditions, $params=array())
	{
		$table = static::table();
		return $table->update_all($attrs, $conditions, $params);
	}
	
	
	protected function validates($data) {
		return true;
	}

	/**
	 * 模型中的数据 column_name => value
	 *
	 * @var array
	 */
	private $attributes = array();
	
	/**
	 * 模型数据是否修改标志
	 * 已修改时包含修改字段
	 * 未修改时为null 
	 *
	 * @var array
	*/
	private $__dirty = null;
	
	/**
	 * 表示是能修改的标志 修改函数包括: save/update/insert/delete
	 *
	 * @var boolean
	 */
	private $__readonly = false;
	
	/**
	 * 是否为新记录的标志 ture是save() 执行 insert 否则执行 update
	 *
	 * @var boolean
	*/
	private $__new_record = true;
	
	/**
	 * 模型写操作发生错误时 {@link ModelErrors} 的实例将被创建.
	 *
	 * @var ModelErrors
	 */
	public $errors;
	
	public function __construct(array $attributes=array(), $guard_attributes=true, $instantiating_via_find=false, $new_record=true)
	{
		$this->__new_record = $new_record;
		
		// initialize attributes applying defaults
		if (!$instantiating_via_find)
		{			
			foreach (static::table()->schema() as $name => $meta) {				
				$this->attributes[$name] = $meta['default'];
			}
		}
	
		$this->set_attributes_via_mass_assignment($attributes, $guard_attributes);
		// since all attribute assignment now goes thru assign_attributes() we want to reset
		// dirty if instantiating via find since nothing is really dirty when doing that
		if ($instantiating_via_find)
			$this->__dirty = array();
	}
	
	/**
	 * $guard_attributes 为 true 如果字段为空则抛出异常.
	 *
	 * @throws UndefinedPropertyException
	 * @param array $attributes An array in the form array(name => value, ...)
	 * @param boolean $guard_attributes Flag of whether or not protected/non-accessible attributes should be guarded
	 */
	private function set_attributes_via_mass_assignment(array &$attributes, $guard_attributes)
	{
		// TODO: not implemented
		$this->attributes = $attributes;
	}
	
	public function &__get($name)
	{
		// check for getter
		if (method_exists($this, "get_$name"))
		{
			$name = "get_$name";
			$value = $this->$name();
			return $value;
		}
	
		return $this->read_attribute($name);
	}
	
	public function __set($name, $value)
	{
		if ($this->__readonly) {
			throw new ModifyReadOnlyModelException("property: ".$name, 0);
		}
		
		if($this->__new_record) {
			return $this->assign_attribute($name,$value);
		}
		else {
			if (array_key_exists($name,$this->attributes))
				return $this->assign_attribute($name,$value);
			
			throw new UndefinedPropertyException("property: ".$name, 0);
		}
	}
	
	public function assign_attribute($name, $value)
	{
		$this->attributes[$name] = $value;
		$this->__dirty[$name] = true; 
	}
	
	/**
	 * Returns a copy of the model's attributes hash.
	 *
	 * @return array A copy of the model's attribute data
	 */
	public function attributes()
	{
		return $this->attributes;
	}
	
	public function &read_attribute($name)
	{
		if (array_key_exists($name,$this->attributes))
			return $this->attributes[$name];
		$null = null;
		return $null;
	}
	
	/**
	 * Returns true if the model has been modified.
	 *
	 * @return boolean true if modified
	 */
	public function is_dirty()
	{
		return empty($this->__dirty) ? false : true;
	}
	
	public function is_new_record()
	{
		return $this->__new_record;
	}
	
	/**
	 * Returns hash of attributes that have been modified since loading the model.
	 *
	 * @return mixed null if no dirty attributes otherwise returns array of dirty attributes.
	 */
	public function dirty_attributes()
	{
		if (!$this->__dirty)
			return null;
	
		$dirty = array_intersect_key($this->attributes,$this->__dirty);
		return !empty($dirty) ? $dirty : null;
	}
	
	/**
	 * 模型数据保存至数据库.
	 *
	 * 该函数会自动检测需要执行INSERT 还是 UPDATE.
	 * If a validation or a callback for this model returns false, then the model will
	 * not be saved and this will return false.
	 *
	 * 如果保存已经存在的模型则只有修改的字段会被保存.
	 *
	 * @param boolean $validate 是否执行验证
	 * @return boolean True 表示保存成功
	 */
	public function save($validate=true)
	{
		//$this->verify_not_readonly('save');
		if (!$this->beforeSave()) return false;
			
		$result = $this->__new_record ? $this->insert($validate) : $this->update($validate);
		if($result) {
			$this->afterSave();
			$this->__new_record = false;
		}
		
		return $result;
	}
	
	protected function beforeSave() 
	{
		$datetime = date('Y-m-d H:i:s', time());
		if ($this->is_new_record()) {
			if (static::table()->hasField('created') && !isset($attributes['created']))
			$this->assign_attribute('created', $datetime);
		}
		
		if (static::table()->hasField('modified') && !isset($attributes['modified']))
			$this->assign_attribute('modified', $datetime);
		
		return true; 
	}
	
	protected function afterSave() {  }
	
	protected function insert($validate=true)
	{	
		$table = static::table();
		$db = static::table()->getDataSource();
		$result = $db->create($table, $this->attributes);
		if($result) {
			$pk = $table->primaryKey;
			if(!isset($this->attributes[$pk]) || !$this->attributes[$pk])
				$this->attributes[$pk] = static::table()->_lastInsertID;			
		}
		return $result;
	}
	
	protected function update($validate=true)
	{
		$table = static::table();
		
		$db = $table->getDataSource();
		
		if ($validate && !$this->_validate())
			return false;
		
		if ($this->is_dirty())
		{
			$class = get_class($this);
			$pk = $table->primaryKey;
		
			if (empty($pk))
				throw new DatabaseException("Cannot update, no primary key defined for: " . get_called_class());
		
// 			if (!$this->invoke_callback('before_update',false))
// 				return false;
		
			$dirty = $this->dirty_attributes();
			$pkValue = $this->read_attribute($pk);
			
			 return $db->update($table, $dirty, "$pk = '$pkValue'");
// 			$this->invoke_callback('after_update',false);
		}
		
		return true;
	}
	
	public function destroy() {
		$table = static::table();
		if($this->__readonly) 
			throw new ModifyReadOnlyModelException("Cannot destory readonly model");
		
		$class = get_class($this);
		$pk = $table->primaryKey;
		
		if (empty($pk))
			throw new DatabaseException("Cannot update, no primary key defined for: " . get_called_class());
		$pkValue = $this->read_attribute($pk);
		$db = static::table()->getDataSource();
		
		return $db->delete($table->useTable, "$pk = ?", array($pkValue));
	}
	
	private function _validate() {
		return true;
	}
	
	/**
	 * 分页 查找记录  王珂 king 9.28
	 * @param array $query 指定查询语句中各组的内容
	 * @param array $params 当$query里面的 conditions带参数时，指定各参数值的数组
	 * @return array|Model 返回一个或者多个Model实例
	 * <code>
	 * $query = array(
	 * 		'fields' => array('accountid', 'nickname'),
	 * 		'conditions' => 'accountid = ?', 	// where语句的字符串，可以带参数
	 * 		'order' => 'created desc'       	// order by语句的字符串
	 * 		'joins' =>                    		// join语句的数组，为一个数组，每一个join项是数组中得一项
	 * 		'page'  => array(
	 * 						'page'	  //页码 
	 * 						'count'   //每页数量
	 * 						'sinceid' //起始ID号
	 * 						'maxid'   //截止ID号
	 * 						)
	 * )
	 * 
	 * $profile = UserProfile::find($query, array(1));
	 * </code>
	 */
	public static function findPage($query = array(), $params = array()) {
	        //table 的实例对象
	    $table = static::table();
	        //当前具体model 的 表的名称的简称
	    $table_name = $table->getDataSource()->fullTableName($table->alias);
	        //主键
	    $table_key = $table->_getPrimaryKey();
	        //分页截取
	    if ( false == empty($query['page']) && (int)$query['page']['count'] > 0 && (int)$query['page']['page'] > 0 ) {
	            //每页数量
	        $query['limit'] = $query['page']['count'];
	            //如果规定了 最大 最小id数
	        if ( false == empty($query['page']['sinceid']) && (int)$query['page']['sinceid'] > 0 ) {
	            if ( false == empty($query['conditions']) ) {
	                $query['conditions'] .= " AND {$table_name}.{$table_key} > {$query['page']['sinceid']}";
	            } else {
	                $query['conditions'] .= " {$table_name}.{$table_key} > {$query['page']['sinceid']}";
	            }
	        } else {
	            
	        }
	        if ( false == empty($query['page']['maxid']) && (int)$query['page']['maxid'] > 0 ) {
	            if ( false == empty($query['conditions']) ) {
	                $query['conditions'] .= " AND {$table_name}.{$table_key} < {$query['page']['maxid']}";
	            } else {
	                $query['conditions'] .= " {$table_name}.{$table_key} < {$query['page']['maxid']}";
	            }
	        }
	            //页码
	        $query['page'] = $query['page']['page'];
	    }
		return $table->_find('all', $query, $params);
	}
}