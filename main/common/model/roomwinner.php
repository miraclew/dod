<?php
/**
 * 房间获奖人
 * @property int $id
 * @property int $roomid
 * @property int $accountid
 * @property int $awards
 * @property int $type
 * @property int $talkid
 * @property int $created
 */
class RoomWinner extends Model {
	public static $useTable = 'room_winners';
	public static $useDbConfig = 'room';
	
}