<?php
/**
 * 系统状态缓存
 * 
 * @property int $last_room_id
 *
 */
class SystemStatus extends RedisHashModel {
	
	protected $namespace = 'message';
	protected $defaultData = array(
			'last_room_id'			=> 0,
	);
	
	public static function get() {
		return new self(0);
	}
}