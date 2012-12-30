<?php
require_once __DIR__.'/cronshell.php';

/**
 * 每日任务
 * 执行时间: 每天0点
 */ 
class DailyShell extends CronShell {
	
	public function run() {
		$this->at0();
		return self::RESULT_SUCCESS;
	}
	
	// 每日0点运行
	public function at0() {
		$log =  $log = __CLASS__."::".__FUNCTION__."\n";
		
		$this->saveDailyAchivement();
		// 系统排名更新
		SystemRankings::instance()->onNextDay();	
		// 更新计数
		$users = SystemRankings::instance()->getTodayActiveUsers();
		
		foreach ($users as $accountid) {
			UserInfo::get($accountid)->onNextDay();
// 			$profile = UserProfile::findByPk($accountid);
// 			$profile->pop_value = 0;
// 			$profile->save();
			
			$log = $accountid.",";
		}
		echo $log."\n";
	
		// 重置今日活跃用户
		SystemRankings::instance()->resetTodayActiveUsers();
		
		UserProfile::update_all(array('pop_value'=> 0), "");
		
		$this->clear_cron_logs();
	}
	
	// 保存每日战绩
	private function saveDailyAchivement() {
		$users = SystemRankings::instance()->getTodayActiveUsers();
		foreach ($users as $u) { // 对每个用户保存战绩
		}
	}
	
	private function clear_cron_logs() {
		CronJobLog::delete_all("created < DATE_SUB(NOW(),INTERVAL 3 DAY)");
	}
}