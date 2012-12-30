<?php
/**
 * 系统计数器
 * 
 * @property int $id
 * @property int $type
 * @property int $objectid
 * @property int $value
 * @property datetime $created
 * @property datetime $modified
 * @author Administrator
 *
 */
class Counter extends Model {
	public static $useTable = 'counter';
	public static $useDbConfig = 'system';
	
	const TYPE_TALK_SHARE = 1; // 作品分享次数
	
	public static function get($type, $objectid, $create=true) {
		$counter = self::first(array('conditions'=>"type=? and objectid=?"), array($type, $objectid));
		if(!$counter) {
			$counter = new self();
			$counter->type = $type;
			$counter->objectid = $objectid;
			$counter->value = 0;
			$counter->save();
		}
		
		return $counter;
	}
	
	public function incr($incrValue=1, $save=true) {
		$this->value += $incrValue;
		
		if($save) $this->save();
		return $this->value;
	}
}