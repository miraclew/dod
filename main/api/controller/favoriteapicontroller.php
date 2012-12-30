<?php
class FavoriteApiController extends ApiController {
    public $components = array('Resource');
    /**
     * 收藏房间的列表
     * 先获取当前用户的id,和所要获取的类型,根据用户id和类型,获取当前用户的收藏的列表
     */
    public function www_rooms() {
        //获取当前用户的id
        $accountid = Auth::user('accountid');
        //获得所要查询的类型,页码,分页数
        $type = $this->_getParam('type', 1, true);
        $page = $this->_getParam('page',1, true);
		$count = $this->_getParam('count', 20, true);
        
        //数据库查询出当前用户的
        $result = Favorite::query(array(
        		'conditions' => "favorite.accountid=? AND favorite.type=?",
        		'fields'     => array('favorite.id', 'u.nickname', 'u.level', 'u.avatar', 'r.id as roomid', 'r.accountid', 'r.type', 'r.title', 'r.expire_time', 'r.pop_value', 'r.bid', 'r.status', 'r.created', 'r.voice', 'r.voice_time', 'r.voice_image', 'r.like_count', 'r.listen_count', 'r.bg_image_id'),
        		'joins'      => array(
        				array('type'  => 'left','alias' => 'r','table' => 'qyh_room.rooms','conditions' => 'r.id = favorite.objectid'),
        				array('type'  => 'left','alias' => 'u','table' => 'qyh_user.user_profiles','conditions' => 'u.accountid = r.accountid')
        		),
        		'order' => 'favorite.created desc',
        		'page' => $page,'limit'=>$count
        		),
        		array($accountid, $type));
        
        $items = array();
        foreach ($result as $value) {
//         	debug($value);
			$item = array(
					'id' => $value['id'],
					'roomid' => $value['roomid'],
					'title' => $value['title'],
					'number' => RoomNumber::number($value['roomid']),
					'time' => Utility::day3ToNow($value['created']),
					'user' => array('accountid' => $value['accountid'], 'avatar' => $value['avatar'], 'nickname'=>$value['nickname'])
				);        	
        	$items[] = $item;
        }
        
        $data['items'] = $items;
        $data['is_last_page'] = count($items) < $count ? 1:0;
        
        $this->success($data);        
    }
    
    /**
     * 创建用户的 收藏.
     * 直接调用新建收藏语句
     */
    public function www_create() {
        //当前用户
        $accountid = Auth::user('accountid');
        $type = $this->_getParam('type');
        $objectid = $this->_getParam('objectid');
        
        $exist = Favorite::exsit("accountid=? and type=? and objectid=?", array($accountid, $type, $objectid));
        if($exist) $this->failed(Err::$OPERATE_ALREADY_DONE);
        
        // 检查数据
        $exist = Room::exsit("id=?", array($objectid));
        if (!$exist) {
        	$this->failed(Err::$DATA_NOT_FOUND);
        }
        
        //插入到数组
        $favorite = new Favorite();
        $favorite->accountid = $accountid;
        $favorite->type = ApiConst::FAVORITE_TYPE_ROOM;
        $favorite->objectid = $objectid;
        
        if($favorite->save()) {
        	$profile = UserProfile::findByPk($accountid);
        	RoomNewMessages::get($objectid)->add($profile->nickname.'收藏了本派对');
        	
            $this->success(array('id' => $favorite->id));            
        }
        else {
            $this->failed(Err::$DATA_SAVE_ERROR);
        }
        
    }
    
    public function www_destroy() {
        $accountid = Auth::user('accountid');
        $id = $this->_getParam('id',0);
        
        $fav = Favorite::findByPk($id);
        if(!$fav)
            $this->failed(Err::$DATA_NOT_FOUND);
        if($fav->accountid != $accountid)
            $this->failed(Err::$OPERATE_OWNNER_ONLY);
        $fav->destroy();
        $this->success();
    }    
    
}