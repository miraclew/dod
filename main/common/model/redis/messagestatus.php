<?php
/**
 * 消息中心数据缓存
 * 
 * @property int $new_private_msg
 * @property string $last_private_msg
 * @property int $last_private_msg_time
 * @property int $new_comment
 * @property string $last_comment
 * @property int $last_comment_time
 * @property int $new_system
 * @property string $last_system
 * @property int $last_system_time
 * 
 * @property int $new_messages
 * @property string $last_message
 * 
 * @property int $last_home_status_id
 * @property int $new_followers
 */
class MessageStatus extends RedisHashModel {
	
	protected $namespace = 'message';
	protected $defaultData = array(
			// total
			'new_messages' 				=> 0,
			'last_message'				=> '',
			
			// pm
			'new_private_msg'			=> 0,
			'last_private_msg'			=> '',
			'last_private_msg_time'		=> 0,
			
			// mention/at
			'new_comment' 				=> 0,
			'last_comment'				=> '',
			'last_comment_time'			=> 0,
				
			// system
			'new_system' 			=> 0,
			'last_system'			=> '',
			'last_system_time'		=> 0,
			
			// new 
			'last_home_status_id'			=> 0,
			'new_followers' 				=> 0,
	);
	
	public static function get($accountid) {
		return new MessageStatus($accountid);
	}
	
	public function get_new_messages() {
		return $this->new_private_msg + $this->new_comment + $this->new_system;
	}
	
	protected function afterKeyCreated() {
		$this->reloadFromDB();
	}
	
	public function new_system_message(SystemMessage $msg) {
		if($msg->toid != $this->id) return;
		
		switch ($msg->type) {
			case ApiConst::MESSAGE_TYPE_SYSTEM:
				$this->new_system++;
				$this->last_system = $msg->message;
				$this->last_system_time = time();
				break;
				
			default:
				;
				break;
		}
	}
	
	public function new_private_message(Message $msg) {
		if($msg->toid != $this->id) return;
		
		$this->new_private_msg++;
		$profile = UserProfile::findByPk($msg->fromid); /* @var $profile UserProfile */
		if($profile) {
			$this->last_private_msg = "来自".$profile->nickname."的悄悄话";
		}
				
		$this->last_private_msg_time = time();
		$this->last_message = json_encode(array('type'=>ApiConst::LAST_MESSAGE_TYPE_LEFT,'user' => $profile->user_avatar,'text'=>$profile->nickname."对您说了悄悄话"));
	}

	public function new_mention(Mention $mention) {		
		if($mention->accountid != $this->id) return;
		
		$this->new_comment++;
		$this->last_comment_time = time();
		$from = UserProfile::findByPk($mention->friendid);
		
		if ($mention->type == ApiConst::AT_TYPE_COMMENT) {
			$room_title = Room::findByPk($mention->roomid)->title;
			$content = Comment::findByPk($mention->objectid);
			if ($content->emotion <= 0) {
				$message = $from->nickname."在$room_title派对中@了您评论";
			}
			else {
				$message = $from->nickname."在$room_title派对中@了您心情";
			}
			$this->last_comment = $message;
		}		
		
		$this->last_message = json_encode(array('type'=>ApiConst::LAST_MESSAGE_TYPE_LEFT,'user' => $from->user_avatar,'text'=>$from->nickname."@了您"));
	}
	
	public function get_last_message() {
		$last_message = array();
		$last_message = json_decode($this->data['last_message']);
		$this->data['last_message'] = '';
		$this->save();
		
		return $last_message;
	}
	
	public function new_follow(Follow $follow) {
		$profile = UserProfile::findByPk($follow->accountid);
		$this->last_message = json_encode(array('type'=>ApiConst::LAST_MESSAGE_TYPE_RIGHT, 'user' => $profile->user_avatar,'text'=>$profile->nickname."关注了您"));
	}
}