<?php
/**
 * 用户时间线
 * 
 */
class UserTimeline extends RedisModel {
	public static $serializer = Redis::SERIALIZER_NONE;
	
	private $namespace = "user:timeline:";
	private $key;
	
	public static function get($id) {
		return new UserTimeline($id);
	}
	
	public function __construct($id) {
		parent::__construct();
		$this->key = $this->namespace.$id;
	}
	
	public function add(Room $item) {
		$this->redis->zAdd($this->key, strtotime($item->created), $item->id);
		$this->redis->zRemRangeByScore($this->key, 0, time()-Room::TTL_NORMAL); // remove expired members		
	}
	
	public function remove(Room $item) {
		$this->redis->zRem($this->key, $item->id);
	}
	
	/**
	 * 分页取数据
	 * @param int $page
	 * @param int $size
	 * @return array of roomid
	 */
	public function page($page, $order='desc') {
		$size = $page['count'];
		$page = $page['page'];
		
		$this->redis->zRemRangeByScore($this->key, 0, time()-Room::TTL_NORMAL); // remove expired members
		
		$start = ($page-1)*$size;
		$stop = $start+$size-1;
	
		if($order == 'desc') {
			$range = $this->redis->zRevRange($this->key, $start, $stop, true);
		} else {
			$range = $this->redis->zRange($this->key, $start, $stop, true);
		}
		
		$data = array();
		foreach ($range as $k => $v) {
			$data[] = $k;
		}
		return $data;
	}
}

