<?php
/**
 * Redis Hash 类型的模型基类
 */

class RedisHashModel extends RedisModel {
	protected $name = null;
	protected $namespace; // key的名字空间
	protected $key;
	protected $id;
	protected $expire=0;
	
	protected $data;
	protected $defaultData = array();
	
	private $loaded = false;
	public $autoSave = true; // __set 自动保存

	public function __construct($id, $load=true) {
		parent::__construct();
		$this->id = $id;
		
		$this->key = $this->namespace ? $this->namespace.':' : '';
		
		if($this->name)
			$this->key .= $this->name.":".$this->id;
		else
			$this->key .= strtolower(get_class($this)).":".$this->id;

		if($load)
			$this->load();
	}
	
	public function attributes() {
		return $this->data;
	}
	
	protected function load($data=null) {
		if($data != null) {
			$this->data = $data;
		}
		else {
			$result = $this->redis->hGetAll($this->key);
			$this->data = array_merge($this->defaultData, $result);
			
			if(empty($result)) {
				if($this->expire > 0)
					$this->redis->expire($this->key, $this->expire);
					
				$this->afterKeyCreated();
			}
		}
		
		$this->loaded = true;
	}
	
	protected function afterKeyCreated() {}
	protected function reloadFromDB() {}
	
	public function save() {
		$this->redis->hMset($this->key, $this->data);
	}

	/**
	 * 销毁该数据
	 */
	public function destroy() {
		$this->data = null;
		$this->redis->del($this->key);
	}

	public function __get($name) {
		if(!$this->loaded)
			$this->load();
		
		if (method_exists($this, "get_$name"))
		{
			$name = "get_$name";
			$value = $this->$name();
			return $value;
		}
		
		if($name == 'id') {
			return $this->id;
		}
		else if(isset($this->data[$name] ) ) {
			return $this->data[$name];
		} else {
			trigger_error( $name . ' variables undefined',  E_USER_NOTICE );
		}
	}

	public function __set($name, $value) {
		$this->data[$name] = $value;
		if($this->autoSave)
			$this->redis->hSet($this->key, $name, $value);
	}
}
