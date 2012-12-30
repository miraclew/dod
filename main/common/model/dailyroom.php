<?php
class DailyRoom extends Model {
	public static $useTable = 'daily_rooms';
	public static $useDbConfig = 'room';
	
	public function relations() {
		return array(
			'room' => array(self::BELONGS_TO, 'Room', 'roomid')
		);
	}
}
