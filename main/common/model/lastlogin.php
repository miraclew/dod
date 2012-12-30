<?php
/**
 * 登陆记录
 * 
 * @property int $accountid
 * @property string $auto_login_token
 * @property string $device_token
 * @property string $ip
 * @property string $platform
 * @property datetime $last_login_time
 * 
 */
class LastLogin extends Model {
	public static $useTable = 'last_logins';
	public static $useDbConfig = 'log';
		
}