<?php
/**
 * 房间赞
 * @property int $id
 * @property int $roomid
 * @property int $accountid
 * @property int $created
 */
class RoomLike extends Model {
	public static $useTable = 'room_likes';
	public static $useDbConfig = 'room';
	
}