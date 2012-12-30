<?php
/**
 * 用户计数和状态缓存
 * 
 * @property int $id
 * @property int $new_gifts
 * @property int $followers
 * @property int $following
 * @property int $rooms
 * @property int $talks
 * @property int $listen_value
 * @property int $listen_value_yesterday
 * @property int $listen_value_change
 * @property int $pop_value
 * @property int $pop_value_yesterday
 * @property int $pop_value_change_yesterday
 * @property int $pop_value_change_today
 * @property int $rank
 * @property int $rank_yesterday
 * @property int $rank_change_yesterday
 * @property int $level
 * @property string $location
 * @property string $lat
 * @property string $lng
 * @property int 	$last_pull_gift_time
 *
 */
class UserInfo extends RedisHashModel {
	const INVALID_LAT = -360;
	const INVALID_LNG = -360;
	
	protected $namespace = 'user';
	protected $defaultData = array(
			// 用户提醒数值
			'new_gifts' 				=> 0,	// 新礼物
			
			// 计数
			'followers' 				=> 0,	// 粉丝数
			'following' 				=> 0, 	// 关注数
			'rooms' 					=> 0, 	// 开的房间数
			'talks' 					=> 0, 	// 创作数			
			
			'listen_value' 				=> 0,	// 倾听值
			'listen_value_yesterday' 	=> 0,
			
			'pop_value' 				=> 0,	// 人气值
			'pop_value_yesterday'		=> 0,	// 昨日人气
			'pop_value_change_yesterday'=> 0,	// 昨日人气变化
			
			'rank_yesterday'			=> 0,	// 昨日排名
			'rank_change_yesterday'		=> 0,	// 昨日排名变化
			
			'level' 					=> 1,	// 等级
						
			// LBS 状态
			'location' 					=> '',	// 地理位置
			'lat' 						=> self::INVALID_LAT,
			'lng' 						=> self::INVALID_LNG,
			
			// 其他状态 
			'last_pull_gift_time'		=> 0, 	// 最后拉礼物的时间
	);
	
	public static function get($accountid) {
		return new self($accountid);
	}
	
	public function get_rank_today_change() {
		return $this->rank - $this->rank_yesterday;
	}
	
	public function get_pop_value_change_today() {
		return $this->pop_value - $this->pop_value_yesterday;
	}
	
	public function get_listen_value_change() {
		return $this->listen_value - $this->listen_value_yesterday;
	}
	
	public function get_rank() {
		return SystemRankings::instance()->getRank($this->id);
	}
	
	protected function afterKeyCreated() {
		$this->reloadFromDB();
	}
	
	public function reloadFromDB() {		
		$accountid = $this->id;
		$this->following = Follow::count("accountid=?",array($accountid));
		$this->followers = Follow::count("targetid=?",array($accountid));
		$this->rooms = Room::count("accountid=?",array($accountid));
		$this->talks = Talk::count("accountid=?",array($accountid));			

		$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
		$this->pop_value = $profile->pop_value;
		$this->level = $profile->level;
	}
	
	public function onNextDay() {
		$this->pop_value_change_yesterday = $this->pop_value - $this->pop_value_yesterday;
		$this->pop_value_yesterday = $this->pop_value;
		
		$this->rank_change_yesterday = $this->rank_yesterday - $this->rank; // 排名应该反过来减
		$this->rank_yesterday = $this->rank;
		
		$this->listen_value_yesterday = $this->listen_value;
	}
}