<?php
require_once __DIR__.'/cronshell.php';

/**
 * 在线状态扫描任务
 * 执行时间: 每分钟执行一次
 */ 
class OnlineUserShell extends CronShell {
	
	public function run() {
		$users = OnlineUser::all(); 
		foreach ($users as $user) { /* @var $user OnlineUser */
			if ($user->expired) {
			    $login = new LoginLog();
			    $login->accountid = $user->accountid;
    			$login->type = LoginLog::LOG_TYPE_2;
    			$login->save();
			    $user->destroy();
			}
		}		
		
		return self::RESULT_SUCCESS;
	}
	
}