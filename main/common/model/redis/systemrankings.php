<?php
/**
 * 系统排名, 列表
 * 
 * 使用sorted set和set实现
 * key 命名规则:
 * CK_{redis-type}_{name}_{key-params-type}
 * CK 表示  cache key
 * {redis-type} 表示该key的数据类型: l=>list, s=>set, z=> sorted set, 空 => string
 */

class SystemRankings extends RedisModel {
	public static $serializer = Redis::SERIALIZER_NONE;
	
	// 房间排名 (人气)
	const CK_Z_RoomRank						= 'sys:roomrank';
	// 用户排名 (人气)
	const CK_Z_UserRank						= 'sys:userrank';

	// 倾听力排名
	const CK_Z_ListenRank					= 'sys:listenrrank';
	// 今日活动用户(登录过的) （登录后加入该队列）
	const CK_S_Today_ActiveUsers			= 'sys:todayactiveusers'; // 不自动过时
	//  昨日新人榜
	const CK_Z_Yesterday_NewbieRank 		= 'sys:yesterdaynewbierank'; // 昨日新人榜
	
	const CK_Z_NormalRooms 					= 'sys:normalrooms';
	
	const MAX_RANKING_ITEMS = 100000; // 最大排名数10万
	
	/**
	 * Return the instance
	 * @return SystemRankings
	 */
	private static $__instance;
	
	public static function instance() {
		if (self::$__instance == null) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}
	
	/**
	 * 获取大厅排行榜
	 */
	public function getRoomRanking() {
		$key = self::CK_Z_RoomRank;
		
		return $this->zsetPaginate($key, 1, 100);
	}
	
	public function getRoomRankingIds() {		
		$ranking = $this->getRoomRanking();		
		$roomids = array();
		foreach ($ranking as $value) {
			$roomids[] = $value['member'];
		}
		
		return $roomids;
	}
	
	/**
	 * 获取房间排名
	 * @param int $roomid
	 */
	public function getRoomRank($roomid) {
		$key = self::CK_Z_RoomRank;		
		$rank = $this->redis->zRevRank($key, $roomid);
		return $rank+1; 
	}
	
	/**
	 * 获得排名
	 * @param int $accountid
	 * @return number
	 */
	public function getRank($accountid) {
		$key = self::CK_Z_UserRank;
		$rank = $this->redis->zRevRank($key, $accountid);
		return $rank === false ? 0:$rank+1; // 排名从1开始
	}
	
	/**
	 * 超越全服多少用户(力量增加的排行)
	 * @param int $accountId
	 * @return number
	 */
	public function getRankSurpass($accountId) {
		$key = self::CK_Z_UserRank;
		$rank = $this->redis->zRevRank($key, $accountId);
		$count = $this->redis->zCard($key);
		if($count <= 0 || $rank===false) return 0;
		return floor(100*($count - $rank)/$count);
	}
	
	/**
	 * 查找排名附近的人
	 * @param int $rank
	 * @param int $limit
	 */
	private function rankingAround($key, $rank, $limit) {
		$start = ($rank-1) - intval($limit*0.2);
		if($start < 0) $start = 0;
		$stop = ($rank-1) + intval($limit*0.8);
		
		$range = $this->redis->zRevRange($key, $start, $stop, true);
		$rank = $start+1;
		$data = array();
		foreach ($range as $k => $v) {
			$data[] = array('rank'=> $rank, 'member'=> $k, 'score'=> $v);
			$rank++;
		}
		return $data;
	}
	
	
	/**
	 * 获取我的排名
	 * @param int $page
	 * @param int $limit
	 * @return array('rank','member','score')
	 */
	public function getMyRanking($rank) {
		$key = self::CK_Z_UserRank;
		return $this->rankingAround($key, $rank, 100);
	}
	
	/**
	 * 获取TOP榜
	 * @param int $page
	 * @param int $limit
	 * @return array('rank','member','score')
	 */
	public function getTopRanking($page, $limit) {
		$key = self::CK_Z_UserRank;
		return $this->zsetPaginate($key, $page, $limit);
	}
	
	public function getNewbieRanking($page, $limit) {
		$key = self::CK_Z_Yesterday_NewbieRank; 
		return $this->zsetPaginate($key, $page, $limit);
	}
		
	// 分页函数
	private function zsetPaginate($key, $page, $limit) {
		$start = ($page-1)*$limit;
		$stop = $start+$limit-1;
		
		$range = $this->redis->zRevRange($key, $start, $stop, true);
		$rank = $start+1;
		$data = array();
		foreach ($range as $k => $v) {			
			$data[] = array('rank'=> $rank, 'member'=> $k, 'score'=> $v);
			$rank++;
		}	
		return $data;
	}
	
	// 加入房间排名(从1开始数)
	public function addRoom($roomid, $value) {
		$key = self::CK_Z_RoomRank;
		$this->redis->zAdd($key, $value, $roomid); // 排名
		$newRank = $this->redis->zRevRank($key, $roomid);
		return $newRank+1;
	}
	
	public function removeRoom($roomid) {
		$key = self::CK_Z_RoomRank;
		return $this->redis->zRem($key, $roomid);
	}
	
	// 加入用户排名(从1开始数)
	public function addUser($accountId, $value) {
		$key = self::CK_Z_UserRank;
		$this->redis->zAdd($key, $value, $accountId); // 排名
		$newRank = $this->redis->zRevRank($key, $accountId);
		return $newRank+1;
	}
	
	/** 今日活跃用户 **/
	public function getTodayActiveUsers() {
		return $this->redis->sMembers(self::CK_S_Today_ActiveUsers);
	}
	
	public function addTodayActiveUser($accountId) {
		$count = $this->redis->sAdd(self::CK_S_Today_ActiveUsers, $accountId);
		return $count;
	}
	
	// 重新生成今日活动用户,跨天结算切割
	public function resetTodayActiveUsers() {
		$this->redis->del(self::CK_S_Today_ActiveUsers);
		
		$onlineUsers = OnlineUser::all();
		//echo "reset todayactiveusers ".count($onlineUsers)."\n";
		foreach ($onlineUsers as $u) {
			$this->addTodayActiveUser($u->accountid);
		}
	}
	
	public function onNextDay() {
		$log = __CLASS__."::".__FUNCTION__."\n";
		$this->processNewbieRanking();
		echo $log."\n";
	}
	
	private function processNewbieRanking() {
		$new_user_time = 7*24*3600;
		
		// 1. remove old users (register time > 7 days)
		$newbies = $this->redis->zRange(self::CK_Z_Yesterday_NewbieRank, 0, -1);
		foreach ($newbies as $value) {
			$profile = UserProfile::findByPk($value); /* @var $profile UserProfile */
			if(time() - strtotime($profile->created) > $new_user_time) {
				$this->redis->zRem(self::CK_Z_Yesterday_NewbieRank, $value);
			}
		}
		
		// 2. add today active newbies
		$users = $this->getTodayActiveUsers();
		foreach ($users as $accountid) {
			$profile = UserProfile::findByPk($accountid); /* @var $profile UserProfile */
			if(time() - strtotime($profile->created) > $new_user_time) continue;
			
			$this->redis->zAdd(self::CK_Z_Yesterday_NewbieRank, UserInfo::get($accountid)->pop_value, $accountid);
		}
		
		// 3. fix size
		$size = $this->redis->zCard(self::CK_Z_Yesterday_NewbieRank);
		if($size > 100) {
			$this->redis->zRemRangeByRank(self::CK_Z_Yesterday_NewbieRank, 0, 100-$size);
		}		
	}
	
}