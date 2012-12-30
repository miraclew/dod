<?php
require_once __DIR__.'/cronshell.php';

/**
 * 房间状态扫描任务
 * 执行时间: 每分钟执行一次
 */ 
class RoomClearingShell extends CronShell {
	
	const TOATAL_WINNERS_COUNT = 3; // 总共发奖人数
	
	public function run() {
		// 1. 设置房间为结算状态
		echo "room normal -> clearing: \n";
		$rooms = Room::find(array('conditions' => "status=? and expire_time<now()", 'limit' => 100, 'order' =>'id asc'), array(ApiConst::ROOM_STATUS_NORMAL));
		foreach ($rooms as $room) { /* @var $room Room */
			echo "room ".$room->id;
			Log::write('normal -> clearing: '.$room->id, 'roomclearing');
			$room->clearing();					
			echo "\r\n";
		}
		
		// 2. 自动关闭房间 
		echo "room clearing -> closed : \n";
		$rooms = Room::find(array('conditions' => "status=? and expire_time<?", 'limit' => 100, 'order' =>'id asc'), array(ApiConst::ROOM_STATUS_CLEARING, date('Y-m-d H:i:s', time()-Room::TTL_CLEARING)));
		foreach ($rooms as $room) { /* @var $room Room */
			echo "room ".$room->id;
			// move below to another offline job			
			$room->close();
			echo "\r\n";
			
			Log::write('clearing -> closed: '.$room->id, 'roomclearing');
		}
		
		return self::RESULT_SUCCESS;
	}
}