<?php
/**
 * 房间禁言
 * @property int $id
 * @property int $roomid
 * @property int $accountid
 * @property string $created
 *
 */
class RoomBlock extends Model {
	public static $useTable = 'room_blocks';
	public static $useDbConfig = 'room';
}