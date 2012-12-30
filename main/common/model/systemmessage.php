<?php
/**
 * 系统信息
 * 
 * @property int $id
 * @property int $toid
 * @property int $fromid
 * @property int $objectid
 * @property int $type
 * @property int $sub_type
 * @property string $message
 * @property boolean $has_read
 * @property int $ack_status
 * @property string $annotations
 * @property datetime $created
 * 
 * @property string $title
 */
class SystemMessage extends Model {
    public static $useTable = 'system_messages';
    public static $useDbConfig = 'message';
    
    public $components = array('Resource');
    
    const ACK_STATUS_NONE = 0; // 未响应
    const ACK_STATUS_1 = 1; // 响应1
    const ACK_STATUS_2 = 2; // 响应2
    
    /**
     *得到消息的详情
     */
    public function get_message() {
        $message = $this->get_message_and_title_and_avatar();
        return $message[1];
    }
	
    /**
     * 得到消息的标题
     */
    public function get_title() {
        $message = $this->get_message_and_title_and_avatar();
        return $message[0];
    }
    
    /**
     * 得到消息的标题和详情 头像
     */
    public function get_message_and_title_and_avatar () {
        $message =  $this->read_attribute('message');
        $sub_type = $this->read_attribute('sub_type');
        $message_arr = Str::$MESSAGE_CONTENT_TEXT;
        
        $title =  Str::$MESSAGE_TITLE_TEXT[$sub_type];
        $content = Str::$MESSAGE_CONTENT_TEXT[$sub_type];
        $avatar =  HTTP_PATH.OFFICIAL_AVATAR;
        if ($this->fromid > 0) {
        	$from = UserProfile::findByPk($this->fromid); /* @var $from UserProfile */
        }        
        
        switch ( $sub_type ) {
        	case ApiConst::MESSAGE_SUB_TYPE_COMMENT_EMOTION:        		
        	case ApiConst::MESSAGE_SUB_TYPE_COMMENT_VOICE:
        		$room = Room::findByPk($this->annotations['roomid']);
        		$content = __($content, $from->nickname, $room->title);
        		break;
        	case ApiConst::MESSAGE_SUB_TYPE_SYSTEM_ACTIVITY:
        		break;
        	case ApiConst::MESSAGE_SUB_TYPE_SYSTEM_UPGRADE:
        		break;
        	case ApiConst::MESSAGE_SUB_TYPE_SYSTEM_REWARDS:
        		break;
       		case ApiConst::MESSAGE_SUB_TYPE_SYSTEM_TALK_DELETED:
       			$room = Room::findByPk($this->annotations['roomid']);
       			$talk = Talk::findByPk($this->objectid);
       			$content = __($content, $room->title, $talk->floor);
       			break;
       		case ApiConst::MESSAGE_SUB_TYPE_SYSTEM_ROOM_RECOMMEND:
       			$room = Room::findByPk($this->annotations['roomid']);
       			$content = __($content, $room->title);
       			break;
       		case ApiConst::MESSAGE_SUB_TYPE_SYSTEM_URGENT:
       			break;
            default;
               };
               
           return array($title, $content, $avatar);
    }
    
    // TODO
    public function get_avatar() {
    	return "http://dev.hoodinn.com/venus/p/12/06/14/p_3910d499918adf6d6addfb6789d8c617_320_320.jpg";
    }
    
    public function get_annotations() {
    	return json_decode($this->read_attribute('annotations'), true);
    }
    
	protected function afterSave() {
    	if($this->is_new_record()) {
//     	    if ( $this->sub_type != ApiConst::MESSAGE_SUB_TYPE_ROOM_INVITE ) {
//         		NewMessage::push_system($this);
//         		// 更新新消息计数
//         		NewMessageCounter::incrSystemMessageCount($this);
//         		// 更新缓存计数
//         		MessageStatus::get($this->toid)->new_system_message($this);
//         		$this->pushMessage();        		
//     	    }
    		MessageStatus::get($this->toid)->new_system_message($this);
    		$this->pushMessage();
    	}
    }
    
    private function pushMessage() {
    	$push = false;
    	switch ($this->type) {
    		case ApiConst::MESSAGE_TYPE_COMMENT:
    			$push = Preference::get($this->toid, Preference::PUSH_COMMENT_MSG) == 1;
    			break;
			case ApiConst::MESSAGE_TYPE_SYSTEM:
				$push = true;
				break;
    		default:
    			break;
    	}
    	
    	if($push && $this->toid != $this->fromid) {
    		Resque::enqueue(QueueNames::ALOHA, JobNames::APNS_PUSH, array('accountid'=>$this->toid, 'message'=> $this->message));
    	}
    }
}