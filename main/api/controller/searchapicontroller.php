<?php
/**
 * 
 * 搜索用户
 * 
 * @property ResourceComponent $Resource
 *
 */
class SearchApiController extends ApiController {
    public $components = array('Resource');
    
    public function www_list() {
        echo __FUNCTION__;
    }
    
    /**
     * 模糊查找搜索用户
     * 根据用户输入的关键字 模糊搜索用户的 昵称
     */
    public function www_users() {
        //获得所填写的关键字 同时检查是否为空
        $nickname = $this->_getParam('keywords', '', true);
        $accountid = Auth::user('accountid');
        
        if ( true == empty($nickname) ) {
            $this->_respond(Err::$INPUT_TOO_SHORT);
        } else {
            //模糊搜索
            $result = UserProfile::find(array('conditions' => "userprofile.nickname LIKE '%{$nickname}%'",
                                              'fields' => array('userprofile.accountid', 'userprofile.nickname', 'userprofile.avatar', 'userprofile.level', 'f.targetid'),
                                              'joins' => array(array('type' => 'left','alias' => 'f','table' => 'qyh_relation.follows', 'conditions' => "userprofile.accountid = f.targetid AND f.accountid ={$accountid}")),
                                              ));
            if ( false == is_array($result) ) {
                $this->_respond(Err::$FAILED);
            } else {
                $data = array();
                //提取搜索结果
                foreach ( $result as $value ) {
                    $item = $value->attributes();
                    $item['badge'] = $this->Resource->getBadge($item['level']);
                    unset($item['level']);
                    if ( true == empty($item['targetid']) ) {
                        $item['value'] = 0;
                    } else {
                        $item['value'] = 1;
                    }
                    unset($item['targetid']);
                    $data['items'][] = $item;
                }
                $this->_respond(Err::$SUCCESS, $data);
            }
        }
    }
    
    public function www_user_nearby() {
        echo __FUNCTION__;
    }
    
    /**
     * 模糊查找搜索 房间
     * 根据用户输入的关键字 模糊搜索房间的抬头
     */
    public function www_rooms() {
        //获得所填写的关键字 同时检查是否为空
        $keywords = $this->_getParam('keywords', '', true);
        if ( empty($keywords) ) {
            $this->failed(Err::$INPUT_REQUIRED);
       	}
       	
       	$conditions = " title LIKE '%{$keywords}%'";
       	$rn = RoomNumber::findByPk($keywords);
       	if ($rn) {
       		$conditions .= " or id=".$rn->roomid;
       	}
       	
       	//模糊搜索
       	$rooms = Room::find(array('conditions' => $conditions));
       	
       	foreach ($rooms as $room) { /* @var $room Room */
       		$profile = UserProfile::findByPk($room->accountid); /* @var $profile UserProfile */
       		$items[] = array(
       				'id' => $room->id,
       				'title' => $room->title,
       				'number' => RoomNumber::number($room->id),
       				'time' => Utility::day3ToNow($room->created),
       				'user' => array('accountid' => $room->accountid, 'avatar' => $profile->avatar, 'nickname'=>$profile->nickname)
       		);
       	}
       		
       	$data = array('items' => $items);
       	//$data['is_last_page'] = count($items) < $page['count'] ? 1:0;
       		
       	$this->success($data);
    }
    
    /**
     * 找第三方朋友
     *
     * @return
     */
    public function www_open_platform_users() {
        $accountid = Auth::user('accountid');
        $platform = $this->_getParam('platform');
        if (!in_array($platform, array('WEIBO', 'RENREN', 'T_QQ')))
            return $this->_respond(Err::$INPUT_FORMAT_INVALID);
        
        $data = array("userId" => $accountid, "platform"=>$platform, 'appId' => APP_ID);
        $data = json_encode($data);
        $result = UsersThirdApi::searchHoodinnFriend($data);
        $responseData = json_decode($result, true);
        
        if ($responseData['code'] == USER_SERVICE_SUCCESS) { //
            $startPage = $responseData['data']['page']['currentPage'];
            $count = $responseData['data']['page']['resultCount'];
            
            $data = $responseData['data']['resultList'];
            $friends = array();
            if (count($data) > 0) {
                $ids = Utility::collectField($data, 'userId');
                $profiles = UserProfile::instance()->getNameAvatar($ids);
                foreach($data as $user) {
                    if (!empty($profiles[$user['userId']])) {
                        $friends[] = $profiles[$user['userId']];
                    }
                }
            }
            $lastPage = 1;
            $this->_respond(Err::$SUCCESS, array('page' => $startPage, 'count' => count($data), 
                                    'lastpage' => $lastPage, 'result' => $friends));
        } else {//失败
            $ret = array($responseData['code'], $responseData['message']);
            return $this->_respond($ret);
        }    
    }
}