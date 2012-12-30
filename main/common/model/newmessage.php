<?php
/**
 * 新消息
 * 
 * @property int $id
 * @property int $toid
 * @property int $fromid
 * @property string $message
 * @property int $msg_count
 * @property int $type
 * @property int $sub_type
 * @property datetime $created
 * @property datetime $modified 
 */

class NewMessage extends Model {
    public static $useTable = 'new_messages';
    public static $useDbConfig = 'message';
    
    
    public static function pop($accountid) {
    	$msgs = self::find(array('conditions' => "toid=?"), array($accountid));
    	self::delete_all("toid=?", array($accountid));
    	return $msgs;
    }
    
    public static function push_private(Message $msg) {
    	$nm = self::first(array('conditions' => "toid=? and fromid=? and type=?"), array($msg->toid, $msg->fromid, ApiConst::MESSAGE_TYPE_PRIVATE));
    	if(!$nm) $nm = new NewMessage(array('toid'=>$msg->toid, 'fromid'=> $msg->fromid, 'type'=>ApiConst::MESSAGE_TYPE_PRIVATE, 'msg_count' => 0));
    	$nm->msg_count++;
    	$nm->save();
    }
    
    public static function push_system(SystemMessage $msg) {
    	$skip_sub_types = array(ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_GIFT_THANKS,
    			ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_JOIN_THANKS,
    			ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_INTRODUCE,
    			);
    	if(in_array($msg->sub_type, $skip_sub_types)) return;
    	
    	$nm = self::first(array('conditions' => "toid=? and type=?"), array($msg->toid, $msg->fromid, $msg->type));
    	if(!$nm) $nm = new NewMessage(array('toid'=>$msg->toid, 'type' => $msg->type, 'msg_count' => 0));
    	$nm->msg_count++;
		$nm->save();
    }
    
    public function get_message() {
    	$text = "";
    	switch ($this->type) {
    		case ApiConst::MESSAGE_TYPE_PRIVATE:
    			$profile = UserProfile::findByPk($this->toid);
    			if(!$profile) return "";
    			$text = $profile->nickname;
    			break;
    		case ApiConst::MESSAGE_TYPE_COMMENT:
    			$text = "您有新评论";
    			break;
    		case ApiConst::MESSAGE_TYPE_SYSTEM:
    			$text = "官方公告";
    			break;
    			
    		default:
    			;
    		break;
    	}
    	
    	return $text." (". $this->msg_count .")";
    }
}
