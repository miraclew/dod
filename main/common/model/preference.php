<?php
/**
 * 用户设置表
 *  
 *  @property int $bg_image_id
 *  @property int $push_private_msg
 *  @property int $push_community_msg
 *  @property int $push_room_msg
 *  @property int $push_comment_msg
 *  @property int $stranger_private_msg
 */
class Preference extends Model {
    public static $useTable = 'preferences';
    public static $useDbConfig = 'user';
    
    const PUSH_PRIVATE_MSG = 'push_private_msg';
    const PUSH_COMMUNITY_MSG = 'push_community_msg';
    const PUSH_ROOM_MSG = 'push_room_msg';
    const PUSH_COMMENT_MSG = 'push_comment_msg';
    const STRANGER_PRIVATE_MSG = 'stranger_private_msg';
	
	public static $defaults =array(
		self::PUSH_PRIVATE_MSG => 1,
		self::PUSH_COMMUNITY_MSG => 1,
		self::PUSH_ROOM_MSG => 1,
		self::PUSH_COMMENT_MSG => 1,
		self::STRANGER_PRIVATE_MSG => 1			
	);
	
	public static function get($accountid, $name) {
		if (!in_array($name, array_keys(self::$defaults)))
			return false;
		
		$p = self::first(array('conditions'=> "accountid=? and name=?"), array($accountid, $name));
		if(!$p) {
			return self::$defaults[$name];
		}
		else 
			return $p->value;
	}
	
	public static function set($accountid, $name, $value) {
		if (!in_array($name, array_keys(self::$defaults)))
			return false;
		
		$p = self::first(array('conditions'=> "accountid=? and name=?"), array($accountid, $name));		
		if(!$p) {
			$p = new self(compact('accountid', 'name', 'value'));
			$p->save();
		}
		else {
			$p->value =$value;
			$p->save();
		}
		
		return true;
	}

	public static function getAll($accountid) {
		$data = array();
		foreach (self::$defaults as $k => $v) {
			$data[$k] = self::get($accountid, $k);
		}
		
		return $data;
	}
}