<?php
/**
 * 在线用户
 * @property int $accountid
 * @property int $last_active_time
 * @property bool $expired 是否过期
 */
class OnlineUser extends Model {
	public static $useTable = 'online_users';
	public static $useDbConfig = 'log';
	
	const EXPIRE_TIME = 1800;// 30 分钟
	
	/**
	 * 更新活动时间
	 * @param int $accountid
	 */
	public static function touch($accountid) {
		$online = OnlineUser::findByPk($accountid);
		if(!$online) {
			$online = new OnlineUser();
			$online->accountid = $accountid;
		}
		$online->last_active_time = date('Y-m-d H:i:s');
		$online->save();
	}
	
	public function get_expired() {
		$diff = time() - strtotime($this->last_active_time);
		if ($diff > self::EXPIRE_TIME) return true;
		return false;
	}	
}