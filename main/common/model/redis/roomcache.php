<?php
/**
 * 房间项缓存
 * 
 * 和房间同时创建,更新。 房间结算后key自动过期。
 * @property int	$id
 * @property int 	$accountid
 * @property string $nickname
 * @property string $avatar
 * @property string $badge
 * @property int 	$type
 * @property string $title
 * @property string $time_remains
 * @property int 	$pop_value
 * @property int 	$bid
 * @property string $voice
 * @property string $voice_time
 * @property string $voice_image
 * @property string $voice_fid
 * @property string $bg_image
 * @property string $bg_image_id
 * @property int 	$like_count
 * @property int 	$listen_count
 * @property int 	$status
 * @property string $invite_by
 * @property int 	$created
 * @property string $tags
 
 */
class RoomCache extends RedisHashModel {
	public static $serializer = Redis::SERIALIZER_NONE;
	protected $name = 'room';
	protected $expire = Room::TTL_NORMAL;
	
	public static function find($ids) {
		$redis = self::redis();		
		
		$redis->multi(Redis::PIPELINE);
		foreach ($ids as $id) {
			$key = "room:".$id;
			$redis->hGetAll($key);
		}
		$data = $redis->exec();
		
		$rooms = array();
		for ($i = 0; $i < count($ids); $i++) {
			if(empty($data[$i])) continue;
			$room = new RoomCache($ids[$i], false);
			$room->load($data[$i]);
			$rooms[] = $room;
		}
		return $rooms;
	}
	
	public function get_time_remains() {
		return Room::get_time_remains(date('Y-m-d H:i:s',$this->created+Room::TTL_NORMAL), $this->status);
	}
	
	public function attributes() {
		$data = parent::attributes();
		$data['id'] = $this->id;
		$data['time_remains'] = $this->time_remains;
		if(!empty($this->voice_image)) {
			$data['voice_image_origin'] = Image::load_from_url($this->voice_image)->get_url(Image::SIZE_ORIGINAL);
		}
		else {
			$data['voice_image_origin'] = "";
		}
		
		return $data;
	}
}