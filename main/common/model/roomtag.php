<?php
/**
 * 房间和标签关系表
 *  
 * @property int $id
 * @property int $roomid
 * @property int $tagid
 * @property datetime $created
 *
 */
class RoomTag extends Model {
    public static $useTable = 'rooms_tags';
    public static $useDbConfig = 'room';
    
    public static function getRoomTags($roomid) {
    	$tags = self::query(
	    			array("fields"=>array('t.name'), "conditions" => "roomid=?",
		    			'joins' => array(
			    				array('type' => 'inner','alias' => 't','table' => 'tags','conditions' => "t.id = roomtag.tagid")
			    			)
		    			),
    				array($roomid)
	    			);
    	if(!empty($tags) && count($tags) > 0) {
    		return $tags[0]['name'];
    	}		
    	
    	return null;
    }
}
