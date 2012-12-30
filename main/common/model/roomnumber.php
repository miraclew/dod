<?php
/**
 * 房间牌号
 * 
 * @property int $number
 * @property int $roomid
 * @property datetime $modified 
 *
 */
class RoomNumber extends Model {
    public static $useTable = 'room_numbers';
    public static $useDbConfig = 'room';
    
    /**
     * 为房间设置一个牌号
     * 
     * @param int $roomid
     * @return int 返回牌号
     */
    public static function reserve($roomid) {
    	$number = self::number($roomid);
    	if ($number > 0) {
    		return $number;
    	}
    	
    	$nr = self::first(array('conditions' => "roomid=0"));
    	if (!$nr) {
    		$nr = new self();
    	}
    	$nr->roomid = $roomid;
    	$nr->save();
    	
    	return $nr->number; 
    }
    
    /**
     * 释放房间所占用的号码
     * @param int $roomid
     */
    public static function release($roomid) {
    	$rn = self::first(array('conditions' => "roomid=0"));
    	if (!$rn) return;
    	
    	$rn->roomid = 0;
    	$rn->save();
    }
    
    /**
     * 获取房间的牌号
     * 
     * @param int $roomid
     * @return int 返回牌号
     */
    public static function number($roomid) {
    	$rn = self::first(array('conditions'=>"roomid=?"), array($roomid));
    	
    	if($rn) 
    		return $rn->number;
    	return 0;
    }  
    
    public static function room_id($number) {
    	$rn = self::findByPk($number);
    	if($rn) return $rn->roomid;
    	return 0;
    } 
}
