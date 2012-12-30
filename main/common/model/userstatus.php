<?php
/**
 * 用户及其关注的动态
 * 
 * @property int $id
 * @property int $accountid
 * @property string $text
 * @property datetime $created
 *  
 */
class UserStatus extends Model {
	public static $useTable = 'user_statuses';
	public static $useDbConfig = 'message';
	
	public static function create($accountid, $text) {
		$us = new UserStatus();
		$us->accountid = $accountid;
		$us->text = $text;
		$us->save();
		
		return $us;
	}
}