<?php
/**
 * KEY/VALUE 键值对
 *
 * @property int $k
 * @property string $v
 * 
 * @property int $int_value
 */
class KV extends Model {
    public static $useTable = 'kvs';
    public static $useDbConfig = 'system';
    
    const LAST_CLEARING_ROOM_ID = "LAST_CLEARING_ROOM_ID";
    
    public static function get($key) {
    	$kv = KV::findByPk($key);
    	if(!$kv) {
    		$kv = new KV(array('k'=>$key, 'v'=>0));
    		$kv->save();    		
    	}
    	return $kv;
    }
    
    public function get_int_value() {
    	return intval($this->v);
    }
    
    // 增加值
    public function incr($value=1, $save=true) {
    	$int_value = $this->int_value;
    	$int_value += $value;
    	$this->v = strval($int_value);
    	
    	if($save)
    		$this->save();
    	
    	return $int_value;
    }
    
}