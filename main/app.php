<?php
/**
 * 系统全局应用类
 *
 */
class App implements EventListener {	
	private static $__instance;
	/**
	 * @return App
	 */
	public static function instance() {
		if (self::$__instance == null) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}
	
	public function initialize() {
		EventManager::instance()->attach($this);
	}
	
	public function implementedEvents() {
		return array(
			EventNames::USER_REGISTER => 'onUserRegister',
			EventNames::USER_LOGIN => 'onUserLogin',
			EventNames::USER_LOGOUT => 'onUserLogout',
				
			EventNames::RELATION_FOLLOW => 'onFollow',
			EventNames::RELATION_UNFOLLOW => 'onUnfollow',
				
			EventNames::ROOM_CREATE => 'onRoomCreate',
			EventNames::ROOM_PAY => 'onRoomPay',
			EventNames::ROOM_CONCLUDE => 'onRoomConclude',
				
			EventNames::TALK_CREATE => 'onTalkCreate',
			EventNames::TALK_SHARE => 'onTalkShare',
				
			EventNames::COMMENT_CREATE => 'onCommentCreate',
			EventNames::ITEM_USING => 'onItemUsing'
		);
	}	
	
	public function onUserRegister($event) {
		$profile = $event->data['profile']; /* @var $profile UserProfile */
		SystemRankings::instance()->addUser($profile->accountid, $profile->pop_value);
	}
	
	public function onUserLogin($event) {
		$accountid = $event->data['accountid'];
		$profile = $event->data['profile'];
		
		OnlineUser::touch($accountid);
		SystemRankings::instance()->addTodayActiveUser($accountid);
		// 首次任务
		$task = Task::findByPk(Task::ID1_FIRST_LOGIN); /* @var $task Task */
		$task->accomplish($accountid);

		SystemRankings::instance()->addUser($profile->accountid, $profile->pop_value);
	}

	public function onUserLogout($event) {	
		$accountid = $event->data['accountid'];
		$user = OnlineUser::findByPk($accountid);
		if($user) {
			$user->destroy();
		}
	}
	
	public function onFollow($event) {
		$follow = $event->data['follow']; /* @var $follow Follow */
		
		if(UserInfo::get($follow->targetid)->followers == 10) {
			$task = Task::findByPk(Task::ID4_10_FOLLOWS); /* @var $task Task */
			$task->accomplish($follow->targetid);
		}
		
		$task = Task::findByPk(Task::ID3_FIRST_FOLLOW); /* @var $task Task */
		$task->accomplish($follow->accountid);
		
		// 发送系统消息
// 		$sm = new SystemMessage();
// 		$sm->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
// 		$sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_FOLLOWER;
// 		$sm->toid = $follow->targetid;
// 		$sm->fromid = $follow->accountid;
// 		$sm->objectid = $follow->id;
// 		$sm->save();
	}
	
	public function onUnfollow($event) {
		$follow = $event->data['follow']; /* @var $follow Follow */
		UserInfo::get($follow->accountid)->following--;
		UserInfo::get($follow->targetid)->followers--;		
	}
	
	public function onRoomCreate($event) {
		$room = $event->data['room'];	 /* @var $room Room */
	}
	
	public function onRoomPay($event) {
		//$room = $event->data['room'];
	}
	
	public function onRoomConclude($event) {
		$room = $event->data['room']; /* @var $room Room */
		
	}
	

	public function onTalkCreate($event) {
		$talk = $event->data['talk']; /* @var $talk Talk */
	}
	
	public function onTalkShare($event) {
		$talk = $event->data['talk']; /* @var $talk Talk */
	}
	
	public function onCommentCreate($event) {
		$comment = $event->data['comment']; /* @var $comment Comment */
	}
	
	public function onItemUsing($event) {		
		$itemUsing = $event->data['itemusing']; /* @var $itemUsing ItemUsing */		
	}
}
