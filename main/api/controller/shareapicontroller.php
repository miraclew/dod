<?php
class ShareApiController extends ApiController {
    public function www_list() {
        echo __FUNCTION__;
    }
    
    public function www_show() {
        echo __FUNCTION__;
    }
    
    /**
     * 分享到其他平台
     *
     * @return
     */
    public function www_create() {
        //获得 转发人,转发平台,信息,所要转发的类型,所要转发信息的ID
        $accountId = Auth::user('accountid');
        $plaftforms = $this->_getParam('plaftforms', '');
        $message = $this->_getParam('message', '');
        $target_type = $this->_getParam('type', '');
        $target_id = $this->_getParam('objectid', '');
        if ( ApiConst::SHARE_OBJECT_TYPE_ROOM != $target_type && ApiConst::SHARE_OBJECT_TYPE_TALK != $target_type ) {
            $this->failed(Err::$INPUT_FORMAT_INVALID);
        }
         
        $platforms_array = explode(',', $plaftforms);
        	
        //获得语音的地址 如果是房主的创建房间语音那就去房间里面找, 如果是话题就去talk里面找
        if ( ApiConst::SHARE_OBJECT_TYPE_ROOM == $target_type ) {
            $voice = Room::findByPk($target_id);
        } elseif ( ApiConst::SHARE_OBJECT_TYPE_TALK == $target_type ) {
            $voice = Talk::findByPk($target_id);
            $profile = UserProfile::findByPk($accountId);
            RoomNewMessages::get($voice->roomid)->add($profile->nickname.'分享了本派对');
        }
            
        if ( true == empty($voice) || false == is_object($voice) ) {
        	$this->failed(Err::$INPUT_FORMAT_INVALID);
        }
        
        //分享表
        $share = new Share();
        $share->accountid = $accountId;
        $share->ownerid = $voice->accountid;
        $share->type = $target_type;
        $share->objectid = $target_id;
        $share->platform = $plaftforms;
        $share->save();
        
        // 微信不需要调用用户服务
        $to_weichat = false;
        if (in_array('WECHAT', $platforms_array)) {
        	unset($platforms_array[array_search('WECHAT', $platforms_array)]);
        	$to_weichat = true;
        }
        
        // 调用用户服务 整理数据
        if (!empty($plaftforms)) {	        
	        $value = array();
	        $value['userId'] = intval($accountId);
	        $value['appId'] = APP_ID;
	        $value['message'] = $message . '。'. __(Str::SHARE_MESSAGE) . ' http://t.htapp.cn/web/share/show?id='. $share->id;
	        $value['platformList'] = $platforms_array;
	         
	        $extendMessage = array();
	        
	        if (in_array('QQ', $platforms_array)) { //如果是人人网 需要特殊字段
	        	$extendMessage['title'] = APP_NAME;
	        	$extendMessage['url'] = APP_WEBSITE;
	        	$extendMessage['site'] = APP_NAME;
	        	$extendMessage['fromurl'] = "http://www.163.com";
	        	$value['extendMessage'] = $extendMessage;
	        	$value['pictures'] = array();
	        }
	        
	        $data = json_encode($value);
	        $result = UsersThirdApi::publishMessage($data);
	        $responseData = json_decode($result, true);
	        
	        if (empty($responseData)) {
	        	throw new ErrRtnException(Err::$FAILED);
	        }
	        
	        Log::write("params:$data", 'share');
	        Log::write("result:$result", 'share');
	        
	        $apiOk = $responseData['code'] == USER_SERVICE_SUCCESS;
        }
        
        if ($apiOk || $to_weichat) { //成功
        	if(ApiConst::SHARE_OBJECT_TYPE_TALK == $target_type) {
        		$talk = Talk::findByPk($target_id);
//         		$message = new SystemMessage();
//         		$message->fromid = $accountId;
//         		$message->toid = $talk->accountid;
//         		$message->objectid = $target_id;
//         		$message->annotations = json_encode(array('plaftforms'=>$plaftforms, 'roomid'=>$talk->roomid));
//         		$message->type = ApiConst::MESSAGE_TYPE_ROOM;
//         		$message->sub_type = ApiConst::MESSAGE_SUB_TYPE_ROOM_TALK_SHARE;
//         		$message->save();
        
        		EventManager::instance()->dispatch(new Event(EventNames::TALK_SHARE, $this, array('talk'=>$talk)));
        	}
        
        	$this->success();
        }

        $this->failed(Err::$FAILED);        
    }
    
    public function www_destroy() {
        echo __FUNCTION__;
    }    
    
}