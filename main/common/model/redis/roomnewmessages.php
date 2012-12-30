<?php
/**
 * 房间最新消息 (顶部滚动显示)
 * 
 * @property int $id
 *
 */
class RoomNewMessages extends RedisModel {	
	private $namespace = "message:room:";
	private $id; // roomid
	private $_messages; //
	
	public static function get($roomid) {
		return new RoomNewMessages($roomid);
	}
	
	public function __construct($id) {
		parent::__construct();
		$this->id = $id;
		
		$this->_messages = $this->redis->sMembers($this->key());
		if (!$this->_messages) {
			$this->_messages = array();
		}
	}
	
	public function messages() {
		return $this->_messages;
	}
	
	public function add($message) {		
		$this->redis->sAdd($this->key(), $message);
		$this->redis->expire($this->key(), 24*3600);
	}
	
	private function key() {
		$key = $this->namespace.$this->id;
		return $key;
	}
}