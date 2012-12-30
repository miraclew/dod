<?php
/**
 * 首页推荐
 *
 * @property int $id
 * @property int $roomid
 * @property string $title
 * @property string $bg_image
 * @property int $sort
 * @property datetime $created 
 */
class HomeRoom extends Model {
	public static $useTable = 'home_rooms';
	public static $useDbConfig = 'room';
	const SORT_TYPE_0 = 0; //直接设置顺序
	const SORT_TYPE_UP = 1; //上升
	const SORT_TYPE_DOWN = 2; //下降
	
	public static function list_all($page) {
		$data = self::query(array(
				'fields' => array('r.id','h.bg_image','h.title','u.avatar','r.voice_fid','r.voice_time','r.voice'),
				'alias' => 'h',
				'joins' => array(
						array('type'  => 'left', 'alias' => 'r', 'table' => 'qyh_room.rooms', 'conditions' => "r.id = h.roomid"),
						array('type'  => 'left', 'alias' => 'u', 'table' => 'qyh_user.user_profiles', 'conditions' => "r.accountid = u.accountid")
						),
				'order' => 'sort desc',
				'page' => $page['page'],
				'limit' => $page['count']
				));
		
		$items = array();
		foreach ($data as $value) {
 			$item = Utility::arrayExtract($value, array('id','bg_image','title'));
 			$item['voice'] = array(
 					'fid' => $value['voice_fid'], 
 					'duration' => $value['voice_time'],
 					'voice' => $value['voice'],
 					'image'=> $value['avatar']
 					);
 			
			$items[] = $item;
		}
		
		return $items;
	}
}