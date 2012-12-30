<?php
/**
 * 用户基本信息
 * @property int $accountid
 * @property string $nickname
 * @property int $gender
 * @property string $region
 * @property string $birthday
 * @property string $introduction
 * @property int $bg_image_id
 * @property int $level
 * @property int $points
 * @property int $golds 钻石
 * @property int $creative_value
 * @property int $listen_value
 * @property int $pop_value
 * @property string $avatar
 * @property string $voice
 * @property int $voice_time
 * @property int $vip
 * @property string $vip_start_time
 * @property string $vip_expire_time
 * @property int $status
 * @property int $type
 * @property string $native_place
 * @property string $occupation
 * @property string $dialect
 * @property string $device_token
 * @property string $created
 * 
 * // virtual
 * @property string $title
 * @property string $badge
 * @property array $user_avatar
 * @property int $room_bid // 开房间的价格
 * @property string $big_avatar
 */
class UserProfile extends Model {
    public static $useTable = 'user_profiles';
    public static $useDbConfig = 'user';    

    const PLATFORM_HOODINN = "HOODINN";
    const PLATFORM_WEIBO = "WEIBO";
    const PLATFORM_RENREN = "RENREN";
    const PLATFORM_QQ = "QQ";
    
    const VIP_0 = 0;//普通会员
    const VIP_1 = 1;//VIP会员
    
    const INITIAL_POINTS = 500;//初始分贝
    const AUTO_AGREE_FRIEND = 2;//同意被自动被加为好友
    
    const STATUS_NORMAL = 0;//正常用户状态
    const STATUS_LOCKED = 1;//锁定用户状态
    
    const TYPE_0 = 0;//普通用户
    const TYPE_1 = 10;//客服用户    
    
    const MAX_POP_VALUE_INCR = 2500; // 每日人气增长最大值
    
    public static function isNicknameExist($nickname, $excluedAccountId=false) {
    	if($excluedAccountId !== false) {
    		return self::exsit("nickname=? and accountid!=?", array($nickname, $excluedAccountId));
    	}
    	else
			return self::exsit("nickname=?", array($nickname));    
    	
    }
    
    /* #region virtual attributes */
    public function get_room_bid() {
    	$today_rooms = Room::today_count($this->accountid);
    	return LevelConfig::get_rooms_bid($today_rooms);
    }
    
    public function get_title()
    {
    	return LevelConfig::pop_value_to_title($this->pop_value);
    }
    
    public function get_badge()
    {
    	$resource = new ResourceComponent(null);
    	return $resource->getBadge($this->level); 
    }
    
    public function get_birthday() {
    	$birthday = $this->read_attribute('birthday');
    	if (empty($birthday)) {
    		return "1980-01-01";
    	}
    	return $birthday;
    }
    
    public function get_gender() {
    	$gender = $this->read_attribute('gender');
    	if (empty($gender) && $gender != 0) {
    		return ApiConst::GENDER_MALE;
    	}
    	
    	return $gender;
    }
    
    public function get_vip() {
    	$vip = $this->read_attribute('vip');
    	$vip_expire_time = $this->read_attribute('vip_expire_time');
    	if(empty($vip)) 
    		return 0;
    	if(!empty($vip_expire_time) && strtotime($vip_expire_time)<time())
    		return 0;
    	
    	return $vip;
    }   

    public function get_big_avatar() {
    	$img = Image::load_from_url($this->avatar);
    	if (!file_exists($img->get_path(Image::SIZE_160x160))) {
    		$img->resize(Image::SIZE_160x160);
    	}
    	return $img->get_url(Image::SIZE_160x160);
    }
    
    /* #endregion virtual attributes */
    
    /**
     * 给用户加人气
     * @param int $value 增加值
     * @return number 实际增加值
     */
    public function add_pop_value($value, $save=true) {
    	$us = UserInfo::get($this->accountid);
    	
    	// apply daily change limitation
    	$today_change = $us->pop_value_change_today;
    	if($today_change + $value > self::MAX_POP_VALUE_INCR) {
    		$value = self::MAX_POP_VALUE_INCR - $today_change;
    	}
    	
    	if($value <= 0) return $value;
    	
    	$old = $this->pop_value; 
    	$this->pop_value += $value;
//     	$oldLevel = $this->level;    	
//     	$this->level = LevelConfig::pop_value_to_level($this->pop_value);
    	$this->save();
    	
    	$this->on_pop_value_changed($old, $this->pop_value);
    	
//     	$us->pop_value = $this->pop_value;    	
//     	$us->level =  $this->level;
    	 
//     	SystemRankings::instance()->addUser($this->accountid, $this->pop_value);	
    	
//     	if($this->level > $oldLevel && $this->level>1) 
//     		$this->onLevelChanged($this->level);

    	
    	
    	return $value;
    }
    
    protected function on_pop_value_changed($old, $new) {
    	if ($old <= 0 && $new > 0) {
    		$this->points += 0.1*500;
    	}
    	else if ($old < 90 && $new >= 90) {
    		$this->points += 0.2*500;
    	}
    	else if($old < 1200 && $new >= 1200) {
    		$this->points += 0.3*500;
    	}
    	else if($old < 3600 && $new >= 3600) {
    		$this->points += 0.4*500;
    	}
    	$this->save();
    }
    
    public function add_listen_value($value, $save=true) {
    	
    }
    
    protected function afterSave() {
    	if ($this->is_new_record()) {    		
    	}
    	else if($this->is_dirty()) {
    		$this->checkCompleteness();
    	}
    }
    
    // 检查完成度
    private function checkCompleteness() {
    	$birthday = $this->read_attribute('birthday');
    	$dialect = $this->read_attribute('dialect');    	
    	$gender = $this->read_attribute('gender');
		$isComplete = !empty($birthday) && !empty($dialect) && !empty($gender);
		
    	if($isComplete && !$this->isOfficialAvatar()) {
			$task = Task::findByPk(Task::ID2_PROFILE_COMPLIETE); /* @var $task Task */
			$task->accomplish($this->accountid);
    	} 
    }
    
    private function isOfficialAvatar() {    	
    	return $this->avatar == HTTP_PATH.OFFICIAL_AVATAR;
    }
    
    protected function onLevelChanged($new_level) {
//     	$sm = new SystemMessage();
// 		$sm->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
// 		$sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_LEVEL;
// 		$sm->toid = $this->accountid;
// 		$sm->fromid = 0;
// 		$sm->objectid = 0;
// 		$sm->annotations = json_encode(array('new_level'=>$new_level));
// 		$sm->save();
		
    	Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::LEVEL_UP,'accountid'=>$this->accountid,'data'=>array()));
    }
    
    public function get_user_avatar() {
    	return array('accountid'=>$this->accountid, 'avatar'=> $this->avatar, 'nickname'=>$this->nickname);
    }
}
