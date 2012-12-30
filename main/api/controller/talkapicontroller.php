<?php
/**
 * 创作控制器
 * @property UploaderComponent $Uploader
 * @property ValidatorComponent $Validator
 * @property ResourceComponent $Resource
 */
class TalkApiController extends ApiController {
	public $components = array('Uploader','Validator','Clearing','Resource');
	
	public function www_list() {
		$accountid = Auth::user('accountid');
		$roomid = $this->_getParam('roomid', 0);
		if($roomid == 0) {
			$this->failed(Err::$INPUT_REQUIRED);
		}
		
		$page = $this->pageParams();
		$talks = Talk::pageQuery(array('conditions'=> "roomid=$roomid", 'order'=>'created asc'), $page);
		$data = array('items'=>array());
		foreach ($talks as $talk) {
			// filter hidden talks
			if ($talk['hide_flag'] == ApiConst::HIDE_FLAG_OTHERS && $talk['accountid'] != $accountid) {
				continue;
			}
			
			$has_emotion = Comment::exsit("talkid=? and emotion>0 and accountid=?", array($talk['id'],$accountid));
			
			$profile = UserProfile::findByPk($talk['accountid']); /* @var $profile UserProfile */
			$item = array('id'=>$talk['id'],
					'user'=>array('accountid'=>$profile->accountid,'avatar'=>$profile->big_avatar,'nickname'=>$profile->nickname),
					'voice'=>array('fid'=>$talk['voice_fid'],'voice'=>$talk['voice'],'duration'=>$talk['voice_time'],'image'=>$talk['voice_image']),
					'pop_value' => $talk['pop_value'],
					'floor'=>$talk['floor'],
					'has_emotion'=> $has_emotion?1:0
					);
			
			$data['items'][] = $item;
		}
		$data['is_last_page'] = count($talks) < $page['count'] ? 1:0;
		$this->_respond(Err::$SUCCESS, $data);
	}
	
	
	public function www_create() {
		$accountid = Auth::user('accountid');
		$data = $this->getParams(array('roomid','themeid', 'voice_time', 'type'));
		
		// 检查是否禁言
		if(RoomBlock::exsit("roomid=? and accountid=?", array($data['roomid'], $accountid))) {
			$this->failed(Err::$ROOM_TALK_BLOCKED);
		}
		
		// check duplicate talk
// 		$last = Talk::last(array('conditions'=>'accountid=?'), array($accountid)); /* @var $last Talk */
// 		if ($last && (time() - strtotime($last->created)) < intval($data['voice_time'])) {
// 			$this->failed(Err::$OPERATE_TOO_FREQUENT);
// 		}

		$room = Room::findByPk($data['roomid']); /* @var $room Room */
		if (!$room) {
			$this->failed(Err::$DATA_NOT_FOUND);
		}
		
		if ($room->status != ApiConst::ROOM_STATUS_NORMAL) {
			$this->failed(Err::$ROOM_STATE_INVALID);
		}
		
		$data['voice'] = $this->Uploader->uploadVoice($this->request->params['form']['voice'], $data['voice_time']);
		$data['voice_image'] = $this->Uploader->uploadImage($this->request->params['form']['voice_image']);	
		
		$talk = new Talk($data);
		$talk->accountid = $accountid;
		$talk->pop_value = 0;
		$talk->hide_flag = ApiConst::HIDE_FLAG_NONE;
		
		if($talk->save()) {
			EventManager::instance()->dispatch(new Event(EventNames::TALK_CREATE, $this, array('talk' => $talk)));
			$this->success();			
		}
		else {
			$this->failed(Err::$DATA_SAVE_ERROR);
		}
	}
	
	public function www_destroy() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		
		$talk = Talk::findByPk($id); /* @var $talk Talk */
		$talk->hide_flag = ApiConst::HIDE_FLAG_OTHERS;
		
		if($talk->save()) {
			$this->success();
		}
		else {
			$this->failed(Err::$DATA_SAVE_ERROR);
		}
	}
	
	public function www_block() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		
		$talk = Talk::findByPk($id); /* @var $talk Talk */ 
		$room = Room::findByPk($talk->roomid); /* @var $room Room */
		if($room->accountid != $accountid) $this->failed(Err::$ROOM_NOT_OWNNER);		
		
		// 检查是否已经屏蔽过
		if(RoomBlock::exsit("roomid=? and accountid=?", array($talk->roomid, $talk->accountid))) {
			$this->failed(Err::$OPERATE_ALREADY_DONE);
		}
		
		$targetid = $talk->accountid;
		$talk->is_blocked = 1;
		$talk->save();
		
		$block = new RoomBlock();
		$block->accountid = $targetid;
		$block->roomid = $talk->roomid;
		$block->save();
		
		$this->success();		
	}
	
	
	public function www_hide() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		
		$talk = Talk::findByPk($id); /* @var $talk Talk */ 
		$room = Room::findByPk($talk->roomid); /* @var $room Room */
		if($room->accountid != $accountid) $this->failed(Err::$ROOM_NOT_OWNNER);		
				
		$talk->is_hidden = 1;
		$talk->save();
		
		$this->success();
	}
	
	public function www_gifts() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id', 0);
		if($id == 0) $this->failed(Err::$INPUT_REQUIRED);
		$items = ItemUsing::query(
				array('fields'=>array('i.id','i.image','u.nickname'), 
				'conditions'=>"talkid=$id", 
				'order' => "total_quantity desc",
				'joins'=>array(
						array('type' => 'left','alias' => 'i','table' => 'items','conditions' => "itemusing.itemid = i.id"),
						array('type' => 'left','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => "u.accountid = itemusing.from_accountid")
				)));
		
		$data = array('items' => array());
		foreach ($items as &$item) {
			$data['items'][] = $item;
		}
		
		$this->success($data);
	}

	/**
	 * 暂时没有
	 */
	public function www_top() {
		$id = $this->_getParam('id');
		$talk = Talk::findByPk($id);

		$this->success();
	}

	public function www_promote() {
		$accountid = Auth::user('accountid');
		$id = $this->_getParam('id');
		
		$talk = Talk::findByPk($id); /* @var $talk Talk */ 
		$room = Room::findByPk($talk->roomid); /* @var $room Room */
		if($room->accountid != $accountid) $this->failed(Err::$ROOM_NOT_OWNNER);		
		
		$talk->type = ApiConst::TALK_TYPE_PERFORM; 
		if($talk->save()) {
			$this->success();
		}
		else {
			$this->failed(Err::$DATA_SAVE_ERROR);
		}
	}
	
}