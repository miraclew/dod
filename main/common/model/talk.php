<?php
/**
 * 房间说话 (创作和评论)
 * 
 * @property int $id
 * @property int $accountid
 * @property int $roomid
 * @property int $type
 * @property string $voice
 * @property int $voice_time
 * @property string $voice_image
 * @property string $voice_fid
 * @property int $floor
 * @property int $themeid
 * @property int $pop_value
 * @property int $at
 * @property boolean $is_blocked
 * @property int $hide_flag
 * @property int $hide_by
 * @property datetime $created
 * 
 * // virtual
 * @property int $type
 * @property array $voice_array
 * 
 */
class Talk extends Model {
    public static $useTable = 'talks';
    public static $useDbConfig = 'room';
    
    public function block() {
    	$this->is_blocked = 1;
    	return $this->save();
    }
    
    public function hide($hide_flag, $hide_by=ApiConst::HIDE_BY_ADMIN) {
    	$this->hide_flag = $hide_flag;
    	$this->hide_by = $hide_by;
    	$this->save();
    }
    
    public static function pageQuery($query, $page) {
    	$query = array_merge(array(
    			'page' => $page['page'],
    			'limit' => $page['count'],
    	),$query);
    		
    	if(empty($query['conditions'])) {
    		$query['conditions'] = "1=1";
    	}
    		
    	if(empty($query['order'])) {
    		$query['order'] = "id desc";
    	}
    		
    	if($page['maxid'] !== false) {
    		$query['conditions'] .= " and talk.id<".$page['maxid'];
    	}
    	if($page['sinceid'] !== false) {
    		$query['conditions'] .= " and talk.id>".$page['sinceid'];
    	}
    	return self::query($query);
    }
    
    public function get_voice_array() {
    	return array('fid'=>$this->voice_fid,'duration'=>$this->voice_time,'voice'=>$this->voice,'image'=>$this->voice_image);
    }    
    
    protected function afterSave() {
    	if($this->is_new_record()) {
    		// new upload
    		$upload = new Upload();
    		$upload->type = Upload::TYPE_TALK_VOICE;
    		$upload->ftype = Upload::FTYPE_VOICE;
    		$upload->accountid = $this->accountid;
    		$upload->objectid = $this->id;
    		//$upload->url = $this->voice;
    		$upload->save();    		 
    		
    		// update floor
    		$this->assign_attribute('voice_fid', $upload->fid);   		
    		$count = Talk::count("id<? and roomid=?", array($this->id, $this->roomid));
    		$this->assign_attribute('floor', $count+1);
    		$this->update(); // don't call save
    		
    		// 
    		$room = Room::findByPk($this->roomid); /* @var $room Room */
    		$toid = $room->accountid;
    		
    		UserTimeline::get($this->accountid)->add($room);
    		
    		$task = Task::findByPk(Task::ID6_FIRST_CREATION); /* @var $task Task */
    		$task->accomplish($room->accountid);
    		
    		$room->join($this->accountid);
    		
    		// 发送系统消息
    		if ($this->accountid != $room->accountid) {
//     			$sm = new SystemMessage();
//     			$sm->type = ApiConst::MESSAGE_TYPE_ROOM;
//     			$sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_ROOM_NEW_CREATION;
//     			$sm->toid = $toid;
//     			$sm->fromid = $talk->accountid;
//     			$sm->objectid = $talk->id;
//     			$sm->annotations = json_encode(array('roomid' => $talk->roomid));
//     			$sm->save();
				
    			$ms = MessageStatus::get($room->accountid);
    			$ms->last_message = json_encode(array('type'=>ApiConst::LAST_MESSAGE_TYPE_LEFT,'user' => $profile->user_avatar,'text'=>$profile->nickname."在您的派对中表演节目"));
    			
    			Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::TALK_CREATE,'accountid'=>$this->accountid,'data'=>array('id'=>$this->id)));
    		}
    		
    		$profile = UserProfile::findByPk($this->accountid);
    		RoomNewMessages::get($this->roomid)->add($profile->nickname.'在第'.$this->floor.'席表演了节目');
    		
    		UserInfo::get($this->accountid)->talks++;    		
    	}
    }
    
    
}
