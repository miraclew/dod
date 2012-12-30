<?php
/**
 * 房间控制器
 * @property UploaderComponent $Uploader
 * @property ValidatorComponent $Validator
 * @property ClearingComponent $Clearing
 * @property ResourceComponent $Resource
 */
class RoomApiController extends ApiController {
	public $components = array('Uploader','Validator','Clearing','Resource');
	
	const ROOM_RANK_MAX = 100; // 首页排行最多房间数
	
	public function www_list_hot() {
		$type = $this->_getParam('type', 0);
		$page = $this->pageParams();
		
		$items = HomeRoom::list_all($page);
		
		$data = array('items' => $items);		
		$data['is_last_page'] = count($items) < $page['count'] ? 1:0;
		
		$this->success($data);
	}
	
	public function www_list_new() {
		$accountid = Auth::user('accountid');
		$type = $this->_getParam('type', 0);
		$page = $this->pageParams();
		$rooms = Room::find(array('order' => "id desc", 'page'=>$page['page'], 'limit'=> $page['count'])); 
		$items = array();
		
		foreach ($rooms as $room) { /* @var $room Room */
			// hide
			if ($room->hide_flag == ApiConst::HIDE_FLAG_OTHERS && $room->accountid != $accountid) continue;
			
			$profile = UserProfile::findByPk($room->accountid); /* @var $profile UserProfile */
			$items[] = array(
					'id' => $room->id,
					'title' => $room->title,
					'number' => RoomNumber::number($room->id),
					'time' => Utility::day3ToNow($room->created),
					'user' => array('accountid' => $room->accountid, 'avatar' => $profile->avatar, 'nickname'=>$profile->nickname)
				);
		}
		
		$data = array('items' => $items);
		$data['is_last_page'] = count($rooms) < $page['count'] ? 1:0;
		
		$this->success($data);		
	}
	
	public function www_public() {
		$type = $this->_getParam('type', 0);
		$page = $this->pageParams();
		
		$data = array('items'=>array());

		// recommonds
		if($page && $page['page']==1) {
			$data['items'] = HomeRoom::query(array(
					'fields' => array('roomid as id','title','bg_image'),
					'order' => 'sort asc',
			));
		}
		
		// rankings
		$rooms = Room::list_public($type, $page);
		foreach ($rooms as $room) {
		    $room['created'] = Utility::day3_to_date($room['created']);
		    $room['time_remains'] = Room::get_time_remains($room['expire_time'], $room['status']);
			$data['items'][] = $room;			
		}
		$data['is_last_page'] = count($rooms) < $page['count'] ? 1:0;
		
		$this->success($data);
	}
	
	public function www_user() {
		$accountid = $this->_getParam('accountid', 0);
		if ($accountid == 0) {
			$accountid = Auth::user('accountid');
		}
		
		if ($accountid == Auth::user('accountid')) {
			$this->myHome($accountid);
		}
		else {
			$this->yourHome($accountid);
		}
	}
	
	private function myHome($accountid) {
		$page = $this->pageParams();
		$data = array();
		
		// 1. clearing rooms, invitation rooms
		if ($page['page'] == 1) {
			$data['items'] = Room::list_user_top($accountid);
			$top_count = count($data['items']);
		}
		else {
			$data['items'] = array();
			$top_count = 0;				
		}

		// 2. user timeline
		$tl = new UserTimeline($accountid);
		$rooms = RoomCache::find($tl->page($page));		
		foreach ($rooms as $room) {
			$data['items'][] = $room->attributes();
		}
		$data['is_last_page'] = count($rooms)-$top_count < $page['count'] ? 1:0;
		
		$this->success($data);
	}
	
	private function yourHome($accountid) {
		$page = $this->pageParams();
		$rooms = Room::list_my_join($accountid, $page);
		$data = array('items' => array());
		foreach ($rooms as $room) {
			$data['items'][] = $room;
		}
		$data['is_last_page'] = count($rooms) < $page['count'] ? 1:0;
		
		$this->success($data);
	}
	
	public function www_random_title() {
		$titles = array(
				'求嗲声嗲气的我爱你',
				'死了都要爱，轻唱小体验！',
				'谁给我来段相声或者笑话？',
				'我想听一首关于世界末日的歌曲！',
				'都来八卦一下领导的糗事！',
				'求心理大师鉴定我是否有强迫症',
				'想听冤鬼路的语音版！',
				'我能模仿各种鸟叫，有谁来听？',
				'晚上8点我开演唱会，期待捧场！',
				'觉得我萧吹的好的请砸我花',
				'今天失恋，求哄Y_Y',
				'今天开讲水煮西游，每天2段',
				'唱情歌，表情感。',
				'求吉他轻弹',
				'方言大学习！',
				'星座运势大解析。');
		
		$title = $titles[array_rand($titles,1)];
		$this->success(array('title' => $title));
	}
	
	public function www_list_my_join() {
		$accountid = Auth::user('accountid');
		$page = $this->pageParams();
		list($rooms, $count) = Room::list_my_join2($accountid, $page);
		$data = array('items' => array());
		foreach ($rooms as $room) {
			$data['items'][] = $room;			
		}
		
		$data['is_last_page'] = $count < $page['count'] ? 1:0;
		
		$this->success($data);
	}
	
	/**
	 * 根据标签ID 获得房间
	 */
	public function www_list_by_tags() {
		$tagid = $this->_getParam('tagid', '', true);
		if (empty($tagid)) {
			$this->failed(Err::$INPUT_REQUIRED);
		}
		
		$page = $this->pageParams();
		
		$tag = Tag::findByPk($tagid); /* @var $tag Tag */
		$tag->count2++;
		$tag->save();
				
		//获取所有房间标签列表
		$result = Room::pageQuery(array(
				'fields'     => array('room.*','u.accountid', 'u.nickname', 'u.avatar','u.level'),
				'conditions' => "tagid = $tagid and room.status=".ApiConst::ROOM_STATUS_NORMAL,
				'order' => 'room.like_count desc',
				'joins' => array(array('type' => 'right', 'table' => 'rooms_tags', 'conditions' => "room.id = rooms_tags.roomid" ),
						array('type' => 'left', 'alias' => 'u',  'table' => 'qyh_user.user_profiles', 'conditions' => "u.accountid = room.accountid"))
		), $page);
			
		$data = array();
		foreach ( $result as $value ) {
			$value['badge'] = $this->Resource->getBadge($value['level']);
			$value['bg_image'] = $this->Resource->getRoomBackgroundImageURL($value['bg_image_id'], true);
			$value['time_remains'] = Room::get_time_remains($value['expire_time'], $value['status']);
			
			$value['voice_image_origin'] = '';
			if (!empty($value['voice_image'])) {
				$value['voice_image_origin'] = Image::load_from_url($value['voice_image'])->get_url(Image::SIZE_ORIGINAL);
			}
			
			$value['tags'] = $tag->name;
			$data['items'][]      = $value;
		}
		
		$data['is_last_page'] = count($result) < $page['count'] ? 1:0; 
			
		$this->success($data);
	}
	
	public function www_create() {
		$accountid = Auth::user('accountid');		
		$title = $this->_getParam('title');
		$type = $this->_getParam('type');
		$voice_time = $this->_getParam('voice_time');
		$bg_image_id = $this->_getParam('bg_image_id');
		
		if(empty($title) || empty($type) || empty($voice_time) ) $this->failed(Err::$INPUT_REQUIRED);

		if ( strlen(iconv('utf-8', 'gb2312', $title)) > TITLE_LONG ) {
		    $this->failed(Err::$INPUT_TOO_LONG);
		}
	    if ( strlen(iconv('utf-8', 'gb2312', $title)) < TITLE_SHORT ) {
		    $this->failed(Err::$INPUT_TOO_SHORT);
		}
		
		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
		$bid = $profile->room_bid;
		if($bid > $profile->points) $this->failed(Err::$TRANS_BALANCE_INSUFFICIENT);
		
		// 避免多次调用接口产生一个房间多条记录
		$last = Room::last(array('conditions'=>'accountid=?'), array()); /* @var $last Room */
		if ($last && $last->title == $title && (time() - strtotime($last->created)) < intval($voice_time)) {
			$this->failed(Err::$ROOM_ROOM_ALREADY_CREATED);
		}
		
		$data = $this->getParams(array('title', 'type', 'voice_time', 'bg_image_id'));		
		
		$room = new Room($data);
		$room->accountid = $accountid;
		$room->voice = $this->Uploader->uploadVoice($this->request->params['form']['voice'], $data['voice_time']);
		if($room->voice === false) $room->voice = "";
		$room->voice_image = $this->Uploader->uploadImage($this->request->params['form']['voice_image']);
		if($room->voice_image === false) {
			$room->voice_image = "";
		}
		else {
			$image = Image::load_from_url($room->voice_image);
			$image->resize(Image::SIZE_80x80);
			$room->voice_image = $image->get_url(Image::SIZE_80x80);  
		}
		
		$room->bid = $bid;
		$room->balance = $room->bid;
		$room->expire_time = date('Y-m-d H:i:s', time() + Room::TTL_NORMAL); 
		$room->like_count = 0;
		$room->listen_count = 0;
		$room->status = ApiConst::ROOM_STATUS_NORMAL;		
		if($room->save()) {			
// 			$tags = explode(',', $this->_getParam('tags'));
// 			if(!empty($tags)) {
// 				$tags = array_unique($tags);
// 				foreach ($tags as $tagid) {
// 					if(empty($tagid)) continue;
// 					$roomTag = new RoomTag();
// 					$roomTag->roomid = $room->id;
// 					$roomTag->tagid = $tagid;
// 					$roomTag->save();
// 				}
// 			}
			
			$cache = new RoomCache($room->id);
// 			$cache->tags = RoomTag::getRoomTags($room->id);
			
			$this->Clearing->roomCreate($room);
			
			EventManager::instance()->dispatch(new Event(EventNames::ROOM_CREATE, $this, array('room' => $room)));
			$this->showRoom($room);
		}	
		else {
			$this->failed(Err::$DATA_SAVE_ERROR);
		}
	}
	
	public function www_show() {		
		$id = $this->_getParam('id');
		$room = Room::findByPk($id);  /* @var $room Room */
		if(!$room) 
			$this->failed(Err::$DATA_NOT_FOUND);
		
		$this->showRoom($room);
	}
	
	private function showRoom(Room $room) {
		$accountid = Auth::user('accountid');
		
		$data = $room->attributes();
		$profile = UserProfile::findByPk($room->accountid);
		$data['nickname'] = $profile->nickname;
		$data['avatar'] = $profile->avatar;
		
		// 道具使用
		$using = ItemUsing::last(array('conditions'=>"roomid=? and type=".ItemUsing::TYPE_ROOM), array($room->id)); /* @var $using ItemUsing */
		if($using) {
			$count = ItemUsing::count("roomid=? and type=".ItemUsing::TYPE_ROOM, array($room->id));
			$gift = Item::findByPk($using->itemid); /* @var $gift Item */
			$item['gift'] = array('image'=> $gift->image, 'count'=> $count, 'nickname'=> UserProfile::findByPk($using->from_accountid)->nickname);
		}
		
		$data['time_remains'] = Room::get_time_remains($data['expire_time'], $data['status']);
		$data['comments'] = Comment::count("roomid=?",array($room->id));
		
		$rank = SystemRankings::instance()->getRoomRank($room->id);
		$data['rank'] = $rank > self::ROOM_RANK_MAX ? 0: $rank;
		
		$data['is_favorite'] = 0;
		$data['favoriteid'] = 0;
		$favorite = Favorite::first(array('conditions'=>"accountid=? and type=? and objectid=?"), array($accountid, ApiConst::FAVORITE_TYPE_ROOM, $room->id));
		if($favorite) {
			$data['is_favorite'] = 1;
			$data['favoriteid'] = $favorite->id;
		}
		
		$isLiked = RoomLike::exsit("roomid=? and accountid=?", array($room->id, $accountid));
		$data['is_liked'] = $isLiked ? 1:0;
		
		$data['bg_image'] = $this->Resource->getRoomBackgroundImageURL($room->bg_image_id, false);
		$data['bg_image_id'] = $room->bg_image_id;
		$data['messages'] = RoomNewMessages::get($room->id)->messages();
		$data['has_result'] = $room->has_result?1:0;
		$data['number'] = RoomNumber::number($room->id);
		
		$room->listen_count++;
		$room->save();
		
// 		//如果是被人邀请的需要发感谢信 同时设置邀请的系统信息全部为已应答
// 		$sm = SystemMessage::first(array('conditions' => 'toid=? and objectid=? and sub_type=? and ack_status=?',
// 		                                 'order' => 'created desc'),
// 		                           array($accountid, $room->id, ApiConst::MESSAGE_SUB_TYPE_ROOM_INVITE, SystemMessage::ACK_STATUS_NONE));
// 		if ( true == $sm ) {
// 		    //发感谢信
// 		    $message = new SystemMessage();
//             $message->toid = $accountid;
//             $message->fromid = $sm->fromid;
//             $message->objectid = $room->id;
//             $message->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
//             $message->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_JOIN_THANKS;
//             $message->message = '';
//             $message->has_read = 0;
//             $message->annotations = '';
//             $message->save();
            
//             //修改所有的邀请变成已应答
//             $result = SystemMessage::update_all(array('ack_status'=> 1),
//             									"toid=? and objectid=? and sub_type=?",
//                                                 array($accountid, $room->id, ApiConst::MESSAGE_SUB_TYPE_ROOM_INVITE));
// 		}
		$this->success($data);		
	}
	
	public function www_like() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		$user = UserProfile::findByPk($accountid); /* @var $user UserProfile */
		
		$room = Room::findByPk($id); /* @var $room Room */
		if(!$room) $this->failed(Err::$DATA_NOT_FOUND);
		
		if($room->status != ApiConst::ROOM_STATUS_NORMAL) $this->failed(Err::$OPERATE_ON_ERROR_STATE);
		
		if ($user->vip > 0) {
			$room->like_count += 3;
		}
		else 
			$room->like_count++;
		$room->save();
		
		$like = new RoomLike();
		$like->roomid = $id;
		$like->accountid = $accountid;
		$like->save();
		
		$old_rank = SystemRankings::instance()->getRoomRank($id);
		$new_rank = SystemRankings::instance()->addRoom($id, $room->like_count);
		
		if ($old_rank > $new_rank) {
		    RoomNewMessages::get($id)->add($user->nickname.' 顶了舞台，舞台广场排名上升到'.$new_rank.'位；');
		    
		    if ($accountid != $room->accountid) {
   			    $sm = new SystemMessage();
			    $sm->type = ApiConst::MESSAGE_TYPE_ROOM;
			    $sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_ROOM_RANK_CHANGE; // 提示房主房间排名上升
			    $sm->toid = $room->accountid;
			    $sm->fromid = 0;
			    $sm->objectid = $room->id;
			    $sm->annotations = json_encode(array('roomid' => $room->id,'rank'=>$new_rank));
			    $sm->save();		    
		    }
		} else {
		    RoomNewMessages::get($id)->add($user->nickname.' 顶了舞台');
		}
		$this->success();
	}
	
	public function www_pay() {
		$accountid = Auth::user('accountid');				
		$id = $this->request->data['id'];
		$targetid = $this->_getParam('accountid');
		$talkid = $this->_getParam('talkid');
		
		Log::write("room pay: accountid=$accountid, roomid=$id, targetid=$targetid", 'roomclearing');
		
		if (empty($id) || empty($targetid) || empty($talkid)) {
			$this->failed(Err::$INPUT_REQUIRED);
		}
		
		/* @var $room Room */
		$room = Room::findByPk($id);
		if (!$room) $this->failed(Err::$DATA_NOT_FOUND);
// 		if($room->accountid != $accountid) $this->failed(Err::$ROOM_NOT_OWNNER);
		
		$result = $room->pay($targetid, $talkid);
		if($result === Err::$SUCCESS) {
			EventManager::instance()->dispatch(new Event(EventNames::ROOM_PAY, array('room'=>$room)));
				
			$this->success();
		}
		else {
			$this->failed($result);
		}
	}	

	public function www_conclude() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
	
		Log::write("room conclude: accountid=$accountid, roomid=$id", 'roomclearing');		
		
		/* @var $room Room */
		$room = Room::findByPk($id);
		// 检查房主
		if($room->accountid != $accountid) $this->failed(Err::$ROOM_NOT_OWNNER);
		// 检查状态
		if($room->status != ApiConst::ROOM_STATUS_CLEARING) $this->failed(Err::$ROOM_STATE_INVALID);		
	
		$conclude = $this->Uploader->uploadVoice($this->request->params['form']['voice'], $this->_getParam('voice_time'));
		if($conclude !== false) {
			$room->conclude = $conclude;
			$room->conclude_time = $this->_getParam('voice_time');
		}
		else {
			$room->conclude = "";
			$room->conclude_time = 0;
		}
		$room->save();
		
		$room->close();
		
		EventManager::instance()->dispatch(new Event(EventNames::ROOM_CONCLUDE, $this, array('room'=>$room)));
	
		$this->success();
	}
	
	/**
	 * 邀请参加房间
	 *
	 * @return
	 */
    public function www_invite() {
        //分别获得 介绍人id 房间id 受邀人id列表
        $accountid = Auth::user('accountid');
        $id = $this->_getParam('id');
        $accountids = explode(',', $this->_getParam('accountids'));
        
        if (empty($accountids)) {
        	$this->failed(Err::$INPUT_INVALID);
        }

        $profile = UserProfile::findByPk($accountid);
        // 批量操作只能由VIP用户执行
//         if(count($accountids)>10 && $profile->vip <= 0) {
//        		$this->failed(Err::$OPERATE_VIP_ONLY);
//         }
        
//        	//分解插入消息
//       	foreach ( $accountids as $accountid ) {
//        		//校验是不是粉丝
//        		if ( true == Follow::is_following($accountid, $accountid) ) {
//        			$message = new SystemMessage();
//        			$message->toid = $accountid;
//        			$message->fromid = $accountid;
//        			$message->objectid = $id;
//        			$message->type = ApiConst::MESSAGE_TYPE_ROOM;
//        			$message->sub_type = ApiConst::MESSAGE_SUB_TYPE_ROOM_INVITE;
//        			$message->message = '';
//        			$message->has_read = 0;
//        			$message->annotations = '';
//        			$message->save();
//        		}
//        	}
        Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::ROOM_INVITE,'accountid'=>$accountid,'data'=>array('id'=>$id,'accountids'=>$accountids)));
       
       	$this->success();        
    }
	
	
	public function www_result() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		
		/* @var $room Room */
		$room = Room::findByPk($id);
		if(!$room) $this->failed(Err::$DATA_NOT_FOUND);
		
		// 检查状态
		if($room->status == ApiConst::ROOM_STATUS_NORMAL) $this->failed(Err::$ROOM_STATE_INVALID);
		if ($room->winner_talkid <= 0) {
			$this->failed(Err::$ROOM_STATE_INVALID);
		}

		$profile = UserProfile::findByPk($room->accountid);
		$talk = Talk::findByPk($room->winner_talkid);
		$winner = UserProfile::findByPk($talk->accountid);
		
		$data = array(
				'conclude' => $room->conclude,
				'conclude_time' => $room->conclude_time,
				'winner' => $winner->user_avatar,
				'talk_floor' => $talk->floor,
				'talk_voice' => $talk->voice_array
				);
		$this->success($data);
	}

    /**
     * 获取房间标签列表
     * 获得所有房间标签的列表
     */
    public function www_tags() {
        $result = Tag::find(array('fields' => array('id', 'name')));
        $data = array();
        foreach ( $result as $value ) {
             $data['items'][] = $value->attributes();
        }
        
        $this->success($data);
    }
}
