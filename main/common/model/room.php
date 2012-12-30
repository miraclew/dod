<?php
/**
 * 房间表
 * 
 * @property int $id
 * @property int $accountid
 * @property int $type
 * @property string $title
 * @property int $bid
 * @property int $balance
 * @property int $pop_value
 * @property datetime $expire_time
 * @property int $is_hidden
 * @property int $status
 * @property int $bg_image_id
 * @property string $voice
 * @property string $voice_fid
 * @property int $voice_time
 * @property string $voice_image
 * @property int $total_creative
 * @property string $conclude
 * @property int $conclude_time // 感言语音时长
 * @property int $winner_talkid
 * @property int $hide_flag
 * @property int $hide_by
 * @property int $like_count
 * @property int $listen_count 
 * @property datetime $created
 * 
 * // virtual
 * @property boolean $has_result // 是否有结果
 * @property boolean $has_talk // 是否有作品
 */
class Room extends Model {
    public static $useTable = 'rooms';
    public static $useDbConfig = 'room';
    
    public $components = array('Resource');
    
	const TTL_NORMAL 	= 172800; // 正常状态时间 48 hours
    const TTL_CLEARING 	= 28800; // 结算周期时间 8 hours

//     const TTL_NORMAL 	= 3600; // 正常状态时间 48 hours
//     const TTL_CLEARING 	= 3600; // 结算周期时间 8 hours
    
    const HIDE_FLAG_NONE 		= 0;
    const HIDE_FLAG_OTHERS 		= 1; // 对所有人隐藏(仅本人能看)
    
    const HIDE_BY_SELF 			= 1;
    const HIDE_BY_ROOM_OWNER 	= 2;
    const HIDE_BY_ADMIN 		= 3;
	
	const TOATAL_WINNERS_COUNT = 3; // 总共发奖人数
    
    public static function list_public($type, $page) {
    	$conditions = "";
    	$order = 'like_count desc, listen_count desc, id desc';
    	
		$rankings = SystemRankings::instance()->getRoomRankingIds();
		
		$in = implode(',', $rankings);
		
		if(!empty($rankings)) {
			$conditions = "room.id in ($in) and room.status=".ApiConst::ROOM_STATUS_NORMAL." and hide_flag=".ApiConst::HIDE_FLAG_NONE;
			$order = "find_in_set(room.id,'$in')";
		}
		
    	$all = self::pageQuery(array(
    				'fields' => array('room.*','u.nickname','u.avatar','u.level'),
	    			'conditions' => $conditions,
    				'order' => $order,
	    			'joins' => array(
	    				array('type' => 'inner','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => "room.accountid = u.accountid")
	    			)
    		), $page);
    	
//     	$all = self::queryBySql("select r.*,u.nickname,u.avatar,u.level from qyh_room.rooms r inner join qyh_user.user_profiles u on r.accountid=u.accountid where r.id in ($in) and r.status=".ApiConst::ROOM_STATUS_NORMAL);
    	
    	foreach ($all as &$value) {
    		$resource = new ResourceComponent(null);
    		$value['badge'] = $resource->getBadge($value['level']);
    		$value['bg_image'] = $resource->getRoomBackgroundImageURL($value['bg_image_id'], true);
    		$value['expire_time'] = $value['expire_time'];
    		$value['time_remains'] = Room::get_time_remains($value['expire_time'], $value['status']);
    		$value['tags'] = RoomTag::getRoomTags($value['id']);
    	}
    		
    	return $all;
    }
    
    /**
     * 结算中的和邀请的房间
     * 
     * @param int $accountid
     * @return array:
     */
    public static function list_user_top($accountid) {
    	$rooms = array();
   		$myClearingRooms = self::get_my_clearing($accountid);
   		$myInvitedRooms = self::get_invited($accountid);    		
   		$rooms = array_merge($myClearingRooms, $myInvitedRooms);
    	
    	foreach ($rooms as &$value) {    		
    		if (!isset($value['invite_by'])) {
    			$value['invite_by'] = '';
    		}
    	}
    	
    	$rooms = self::process_room_array($rooms);
    	return $rooms;
    }
    
    public static function list_my_join2($accountid, $page) {
    	$uploads = Upload::find(array('conditions'=>"type!=? and accountid=?",'page'=>$page['page'],'limit'=>$page['count'],'order'=>'created desc'),
    			array(upload::TYPE_COMMENT_VOICE, $accountid));
    	
    	$rooms = array();
    	foreach ($uploads as $value) { /* @var $value Upload */    		
    		if ($value->type == Upload::TYPE_TALK_VOICE) {
    			$talkid= $value->objectid;
    			$talk = Talk::findByPk($talkid);
    			if ($talk->floor == 1) {
    				continue;
    			}
    			$voice = array('fid'=>$talk->voice_fid,'duration'=>$talk->voice_time,'voice'=>$talk->voice);
    			$roomid = $talk->roomid;
    			$room = Room::findByPk($roomid);    			
    		} else {
    			$roomid= $value->objectid;
    			$room = Room::findByPk($roomid);
    			$voice = array('fid'=>$room->voice_fid,'duration'=>$room->voice_time,'voice'=>$room->voice);
    		}
    		
    		$rooms[] = array('roomid'=>$roomid,'title'=>$room->title,'type'=>$value->type,'created'=>$value->created,'voice'=>$voice);
    	}
    	
    	return array($rooms, count($uploads));    	 
    }
    
	public static function list_my_join($accountid, $page) {
		$rooms = self::pageQuery(array(
				'fields' => array('room.*','u.nickname','u.avatar','u.level'),
				'conditions' => "room.accountid=$accountid or t.accountid=$accountid or c.accountid=$accountid",
		        'order' => 'room.id desc',
				'group' => 'room.id',
				'joins' => array(
						array('type' => 'left','alias' => 'c','table' => 'comments','conditions' => "room.id = c.roomid"),
						array('type' => 'left','alias' => 't','table' => 'talks','conditions' => "room.id = t.roomid"),
						array('type' => 'left','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => "room.accountid = u.accountid")
				)
		), $page);
		
		self::process_room_array($rooms);		
		foreach ($rooms as $key => &$room) {
			unset($room['expire_time']);
			unset($room['level']);
			unset($room['conclude']);
			unset($room['conclude_time']);
			unset($room['is_hidden']);
			unset($room['level']);
		}
		
		// filter all hidden rooms
		$data = array();
		foreach ($rooms as $room) {
			if ($room['hide_flag'] == ApiConst::HIDE_FLAG_OTHERS && $room['accountid'] != $accountid) {
				continue;
			}
			$data[] = $room;
		}
		
		return $data;
	}
	
    private static function process_room_array($rooms) {
    	foreach ($rooms as &$value) {
    		$value['time_remains'] = self::get_time_remains($value['expire_time'], $value['status']);
    		$value['voice_image_original'] = '';
    		if (!empty($value['voice_image'])) {
    			$value['voice_image_original'] = Image::load_from_url($value['voice_image'])->get_url(Image::SIZE_ORIGINAL);
    		}
    	
    		$resource = new ResourceComponent(null);
    		$value['badge'] = $resource->getBadge($value['level']);
    		$value['tags'] = RoomTag::getRoomTags($value['id']);    		
    	}
    	return $rooms;
    }
    
    public static function get_time_remains($expire_time, $status) {
    	if($status == ApiConst::ROOM_STATUS_CLEARING) {
    		return "谢幕中";
    	}
    	else
    		return Utility::room_remains_time($expire_time);    	 
    }
    
    private static function get_invited($accountid) {
    	$sms = SystemMessage::find(array("conditions" => "toid=? and sub_type=? and ack_status=?"), array($accountid, ApiConst::MESSAGE_SUB_TYPE_ROOM_INVITE, SystemMessage::ACK_STATUS_NONE));
    	$rooms = array();
    	$roomid_arr = array();
    	foreach ($sms as $value) { /* @var $value SystemMessage */
    		if(!$value->objectid) continue;
    	    //房间可能多人 邀请了. 这时候只需要第一个
    	    if ( false == in_array($value->objectid, $roomid_arr) ) {
    	        $roomid_arr[] = $value->objectid;
        		$conditions = "room.status=".ApiConst::ROOM_STATUS_NORMAL." and room.id=".$value->objectid;
    	    	$data = self::query(array(
    	    			'fields' => array('room.*','u.nickname','u.avatar','u.level'),
    	    			'conditions' => $conditions,
    	    			'joins' => array(
    	    					array('type' => 'inner','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => "room.accountid = u.accountid")    					
    	    			)
    	    	));
    	    	if($data && count($data)>0) {
    	    		$room = $data[0];
    	    		$profile = UserProfile::findByPk($value->fromid); /* @var $profile UserProfile */
    	    		$room['invite_by'] = $profile->nickname;
    	    		$rooms[] = $room;
    	    		
    	    	}
    	    }
    	}
    	return $rooms;
    }

    // 获取需要我发奖并感言的房间
	private static function get_my_clearing($accountid) {		
		$rooms = self::query(array(
				'fields' => array('room.*','u.nickname','u.avatar','u.level'),
				'conditions' => "room.accountid=? and room.status=? and room.conclude is null",
				'joins' => array(
						array('type' => 'inner','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => "room.accountid = u.accountid")
				)
		), array($accountid, ApiConst::ROOM_STATUS_CLEARING));
		return $rooms;
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
			$query['conditions'] .= " and room.id<".$page['maxid'];
		}
		if($page['sinceid'] !== false) {
			$query['conditions'] .= " and room.id>".$page['sinceid'];
		}
		
		return self::query($query);
	}
	
	public static function today_count($accountid) {
		return Room::count("accountid=? and DATE(created) = CURDATE()", array($accountid));
	}

	/**
	 * 更改房间状态为结算中
	 * 
	 */
	public function clearing() {
		SystemRankings::instance()->removeRoom($this->id);
		$this->status = ApiConst::ROOM_STATUS_CLEARING;
		$this->save();
		
		// 如果房间没有作品, 直接关闭
		if (!$this->has_talk) {
			if ($this->balance > 0) { // 返还point
				$profile = UserProfile::findByPk($this->accountid);
				$profile->points += $this->balance;
				
				$trans = new PointsTrans();
				$trans->type = PointsTrans::PT_ROOM_NO_TALK_REFUND;
				$trans->accountid = $this->accountid;
				$trans->amount = $this->balance;
				$trans->balance = $profile->points;
				$trans->objectid = $this->id;
				$trans->save();
			}			
			
			$this->close();
			return;
		}
		
		Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::ROOM_CLEARING,'accountid'=>$this->accountid, 'data'=>array('id'=>$this->id)));
	}
	
	/**
	 * 选取派队之星
	 * @param int $accountid
	 * @param int $talkid
	 */
	public function pay($accountid, $talkid) {
		if($this->status != ApiConst::ROOM_STATUS_CLEARING) {
			return Err::$ROOM_STATE_INVALID;
		}
		
		// 检查targetid是否参与过该room
		if (!Talk::exsit("accountid=? and type=? and roomid=?", array($accountid,ApiConst::TALK_TYPE_CREATION,$this->id))) {
			return Err::$TRANS_OPERATE_ON_INVALID_USER;
		}
		
		// 检查是否已经发奖
		if($this->winner_talkid) {
			return Err::$ROOM_ALREADY_PAYED;
		}

		if ($this->balance > 0) {
			$trans = new PointsTrans();
			$trans->type = PointsTrans::PT_ROOM_OWNER_PAY;
			$trans->accountid = $accountid;
			$trans->amount = $this->balance;
			$trans->balance = $profile->points;
			$trans->objectid = $this->id;
			$trans->save();
		}
		
		$this->winner_talkid = $talkid;
		$this->balance = 0;
		$this->save();
		
		
		/* @var $profile UserProfile */
		$profile = UserProfile::findByPk($accountid);
		$profile->pop_value += 0.1 * $this->get_hot_value(); 
		$profile->save();
			
		return Err::$SUCCESS;
	}
	
	// 活跃度
	private function get_hot_value() {
		$total_pop_value = Talk::sum('pop_value', "roomid=?", array($this->id));
		$total_participants = Talk::count("roomid=?", array($this->id));
		$total_comments = Comment::count("roomid=?", array($this->id));
		
		return (intval($total_pop_value) + intval($total_participants) + intval($total_comments));
	}
	
	/**
	 * 关闭房间
	 * 分配奖金，结算
	 * 
	 * @return bool 是否成功
	 */
	public function close() {
		if($this->status != ApiConst::ROOM_STATUS_CLEARING) return false;
		
		// 是否有作品		
		if ($this->has_talk) {
			// 如果没有发奖
			if (!$this->winner_talkid) {
				$this->auto_pay();
			}
			
			if($this->conclude_time == 0 && $this->conclude == '') {
				$resource = new ResourceComponent(null);
				$rand = $resource->getRandRoomConcludeVoice();
				$this->conclude = $rand['voice'];
				$this->conclude_time = $rand['duration'];
			};
		}
		
		Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::ROOM_CLOSE,'accountid'=>$this->accountid, 'data'=>array('id'=>$this->id)));
			
		$this->status = ApiConst::ROOM_STATUS_CLOSED;
		$this->save();

		RoomNumber::release($this->id);
		return true;
	}
	
	public function get_has_talk() {
		return Talk::exsit("roomid=? and accountid != ?", array($this->id, $this->accountid));
	}
	
	/**
	 * 归还
	 * 
	 * 没有作品的房间退回房主的出价，并结束该房间  
	 */
	private function refund() {		
		if($this->balance > 0) {
			$profile = UserProfile::findByPk($this->accountid);
			$profile->points += $this->balance;
			
			$trans = new PointsTrans();
			$trans->type = PointsTrans::PT_ROOM_NO_TALK_REFUND;
			$trans->accountid = $this->accountid;
			$trans->amount = $this->balance;
			$trans->balance = $profile->points;
			$trans->objectid = $this->id;
			$trans->save();
		}
		
		$this->balance = 0;
		$this->status = ApiConst::ROOM_STATUS_CLOSED;
		$this->save();
		
		SystemRankings::instance()->removeRoom($this->id);
		Log::write('refund: '.$this->id, 'roomclearing');
	}
	
	/**
	 * 隐藏该房间
	 */
	public function hide($hide_flag, $hide_by=ApiConst::HIDE_BY_ADMIN) {
		$this->hide_flag = $hide_flag;
		$this->hide_by = $hide_by;
		$this->save();
		
		if ($hide_flag != ApiConst::HIDE_FLAG_NONE) {
			$cache = new RoomCache($this->id, false);
			$cache->destroy();
		}
	}
	
	public function get_has_result() {
		if ($this->status != ApiConst::ROOM_STATUS_CLOSED) {
			return false;
		}
		
		return $this->winner_talkid > 0;
	}
	
	public function get_participant_ids() {		
		$talks = Talk::query(array('conditions'=>'roomid=?', 'fields'=>array('distinct(accountid)')), array($this->id));
		$ids = Utility::collectField($talks, 'accountid');
		return $ids;
	}
	
	public function join($accountid) {
		// TODO need improve
		$rooms = Room::list_my_join($accountid, array('page'=>1,'count'=>20,'maxid'=>false,'sinceid'=>false));
		if (count($rooms) > 10) {
			$task = Task::findByPk(Task::ID10_ROOM_JOIN); /* @var $task Task */
			$task->accomplish($accountid);
		}
	}
	
	private function auto_pay() {
		$talk = Talk::first(array('fields' =>array('id','accountid'), 'conditions'=>"roomid=? and type=? and accountid!=?", 'order'=>'pop_value desc'), array($this->id, ApiConst::TALK_TYPE_CREATION, $this->accountid));
		if (!$talk) return;
		
		return $this->pay($talk->accountid, $talk->id);
	}
	
	// 系统自动发奖
	private function auto_award_deprected() {
		$winners = RoomWinner::find(array('conditions'=>"roomid=?"), array($this->id));
		$winner_ids = Utility::collectField($winners, 'accountid');
		
		Log::writeInfo(" auto award:  \r\n exist winner=".implode(',', $winner_ids).".");
		
		// 剩余发奖人数
		$left = self::TOATAL_WINNERS_COUNT - count($winners);
		if($left <= 0) return;
		
		$talks = Talk::find(array('fields' =>array('id','accountid'), 'conditions'=>"roomid=? and type=?", 'order'=>'pop_value desc'), array($this->id, ApiConst::TALK_TYPE_CREATION));		
		Log::writeInfo(" win talk count=".count($talks).'. ');
		if(count($talks) <= 0) return;
		
		// 金额
		$awards = intval($this->balance/$left);
		foreach ($talks as $talk) {
			if(count($winner_ids) >= self::TOATAL_WINNERS_COUNT) break;
			if(in_array($talk->accountid, $winner_ids)) continue;
			if($talk->accountid == $this->accountid) continue;
			$winner_ids[] = $talk->accountid;
			
			/* @var $talk Talk */
			$winner = new RoomWinner();
			$winner->roomid = $this->id;
			$winner->accountid = $talk->accountid;
			$winner->type = ApiConst::ROOM_AWARD_TYPE_AUTO_AWARD;
			$winner->awards = $awards;
			$winner->save();
			
			/* @var $profile UserProfile */
			$profile = UserProfile::findByPk($talk->accountid);
			$profile->points += $awards;
			$profile->save();
			
			$trans = new PointsTrans();
			$trans->type = PointsTrans::PT_ROOM_SYSTEM_PAY;
			$trans->accountid = $talk->accountid;
			$trans->amount = $awards;
			$trans->balance = $profile->points;
			$trans->objectid = $talk->id;
			$trans->save();
			
			$this->balance -= $awards;
			
			Log::writeInfo(" award to: talk=".$talk->id.",winner= ".$talk->accountid.". ");
		}
		
		$this->save();
	}
	
	protected function afterSave() {
		if ($this->is_new_record()) {
			// new upload
			$upload = new Upload();
			$upload->type = Upload::TYPE_ROOM_VOICE;
			$upload->ftype = Upload::FTYPE_VOICE;
			$upload->accountid = $this->accountid;
			$upload->objectid = $this->id;
			//$upload->url = $this->voice;
			$upload->save();
			
			// update fid
			$this->assign_attribute('voice_fid', $upload->fid);
			$this->update(); // don't call save
			
			$this->create_cache();
			
			// create talk
			$talk = new Talk();
			$talk->accountid = $this->accountid;
			$talk->roomid = $this->id;
			$talk->voice = $this->voice;
			$talk->voice_fid = $this->voice_fid;
			$talk->voice_image = $this->voice_image;
			$talk->voice_time = $this->voice_time;
			$talk->save();
			
			// reserve number
			RoomNumber::reserve($this->id);
			
			SystemStatus::get()->last_room_id = $this->id;
			
			$profile = UserProfile::findByPk($this->accountid); /* @var $profile UserProfile */
			$profile->pop_value += self::today_count($this->accountid)*5;
			$profile->save();

			// task stuff
			$task = Task::findByPk(Task::ID5_FIRST_ROOM); /* @var $task Task */
			$task->accomplish($this->accountid);
			$task2 = Task::findByPk(Task::ID12_DAILY_ROOM); /* @var $task2 Task */
			$task2->accomplish($this->accountid);
				
			$this->join($this->accountid);
			Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::ROOM_CREATE,'accountid'=>$this->accountid, 'data'=>array('id'=>$this->id)));				
		}
		else if($this->is_dirty()) {
			$c = new RoomCache($this->id);
			$c->autoSave = false;
			$c->pop_value = $this->pop_value;
			$c->like_count = $this->like_count;
			$c->listen_count = $this->listen_count;
			$c->status = $this->status;
			$c->save();
		}
	}

	private function create_cache() {
		$c = new RoomCache($this->id);
		$c->autoSave = false;
		$profile = UserProfile::findByPk($this->accountid); /* @var $profile UserProfile */
		$c->accountid = $this->accountid;
		$c->nickname = $profile->nickname;
		$c->avatar = $profile->avatar;
		$resource = new ResourceComponent(null);
		$c->badge = $resource->getBadge($profile->level);
		$c->type = $this->type;
		$c->title = $this->title;
		$c->pop_value = $this->pop_value;
		$c->bid = $this->bid;
		$c->voice = $this->voice;
		$c->voice_fid = $this->voice_fid;
		$c->voice_image = $this->voice_image;
		$c->voice_time = $this->voice_time;
		$c->bg_image_id = $this->bg_image_id;
		$c->bg_image = $resource->getRoomBackgroundImageURL($this->bg_image_id, true);
		$c->like_count = $this->like_count;
		$c->listen_count = $this->listen_count;
		$c->status = $this->status;
		$c->created = strtotime($this->created);		
		$c->save();
	}
}