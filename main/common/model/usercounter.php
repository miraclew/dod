<?php
/**
 * 用户计数器
 * 
 * @property int $id
 * @property int $type
 * @property int $accountid
 * @property int $objectid
 * @property int $value
 * @property datetime $created
 * @property datetime $modified
 *  
 */
class UserCounter extends Model {
	public static $useTable = 'user_counter';
	public static $useDbConfig = 'user';
    
	const TYPE_USER_ROOM_POP 		= 1; // 用户在房间中获得的人气值
	const TYPE_TALK_NEW_COMMENTS 	= 2; // 用户创作的新评论数
	const TYPE_ROOM_NEW_COMMENTS 	= 3; // 房主发言新评论
	
	public static function get($type, $accountid, $objectid, $create=true) {
		$counter = self::first(array('conditions'=>"type=? and accountid=? and objectid=?"), array($type, $accountid, $objectid));
		if(!$counter && $create) {
			$counter = new self();
			$counter->type = $type;
			$counter->accountid = $accountid;
			$counter->objectid = $objectid;
			$counter->value = 0;
			$counter->save();
		}
	
		return $counter;
	}
	
	public function incr($incrValue=1, $save=true) {
		$this->value += $incrValue;
		Log::write(json_encode($this->attributes()));
		if($save) $this->save();
		return $this->value;
	}
}
