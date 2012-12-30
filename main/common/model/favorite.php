<?php
/**
 * 个人收藏
 * 
 * @property int $id
 * @property int $accountid
 * @property int $type
 * @property int $objectid
 * @property datetime $created
 *  
 */
class Favorite extends Model {
    public static $useTable = 'favorites';
    public static $useDbConfig = 'user';
    
    /**
     * 插入一条新的收藏
     * 先去获取本表的结构,再根据传进来的数据进行校验,是否合格,然后插入数据
     * @param int $accountid 当前登录用户
     * @param int $type    收藏类型
     * @param int $objectid 被收藏的ID
     *
     * @return array(    
     *                     code    => int,                                    //状态代码 正确与否
     *                     msg     => string                                //提示信息
     *                 )
     */
    public static function _favorite($accountid, $type, $objectid) {
        //获得该表的字段类型等内容 数组
        $table_field_arr = static::table()->schema();
        //获取当前用户的id, 收藏类型, 被收藏的ID
        $attributes = array('accountid' => $accountid, 'type' => $type, 'objectid' => $objectid);
    
        //基础校验字段
        $result = Utility::check_table_field($table_field_arr, $attributes);
        if ( $result[0] < 0 ) {
            return $result;
        }
        //插入数据
        if( null == self::create($attributes)) {
            return Err::$FAILED;
        } else {
            return Err::$SUCCESS;
        }
    }
    
    protected function afterSave() {
    	if($this->type == ApiConst::FAVORITE_TYPE_ROOM) {
//     		$room = Room::findByPk($this->objectid);
//     		$exist = SystemMessage::exsit('fromid=? and toid=? and objectid=? and sub_type=?',
//     				array($this->$accountid, $room->accountid, $this->objectid, ApiConst::MESSAGE_SUB_TYPE_ROOM_FAVORITE));
//     		if (!$exist && ($this->accountid != $room->accountid)) {
//     			$message = new SystemMessage();
//     			$message->fromid = $this->accountid;
//     			$message->toid = $room->accountid;
//     			$message->objectid = $this->id;
//     			$message->type = ApiConst::MESSAGE_TYPE_ROOM;
//     			$message->annotations = json_encode(array('roomid'=>$room->id));
//     			$message->sub_type = ApiConst::MESSAGE_SUB_TYPE_ROOM_FAVORITE;
//     			$message->save();
//     		}
    		Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::FAVORITE_CREATE,'accountid'=>$this->accountid,'data'=>array('id'=>$this->id)));
    	}
    }
}