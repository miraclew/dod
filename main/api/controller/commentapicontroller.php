<?php
class CommentApiController extends ApiController {
	public $components = array('Resource','Uploader');
    /**
     * 获取评论列表
     * @param id int 评论id
     * @return
     */
    public function www_list() {
    	$accountid = Auth::user('accountid');
        $roomid = $this->_getParam('roomid', '', true);
        $type = $this->_getParam('type');
        $talkid = $this->_getParam('talkid', '', true);
        $page = $this->pageParams();
        
        if (!$roomid) {
        	$this->failed(Err::$INPUT_REQUIRED);
        }
        
        // clear counter
        if(!empty($roomid)) {
        	$uc = UserCounter::get(UserCounter::TYPE_ROOM_NEW_COMMENTS, $accountid, $roomid, false);
        	if($uc) {
        		$uc->value = 0;
        		$uc->save();
        	}
        }

        $at_condition = "";
		if($type == ApiConst::COMMENT_TYPE_EMOTION) {
			$at_condition = ' and at_type='.ApiConst::AT_TYPE_TALK.' and emotion>0';        	
        }       	
        
        if ($talkid) {
        	$result = Comment::find(array('conditions'=>"roomid=? and (hide_flag=0 or (hide_flag=1 and accountid=?) ) $at_condition and talkid=?", 'page'=>$page['page'],'limit'=> 10,'order'=>'id desc'), 
        			array($roomid, $accountid, $talkid));
        	
        	$uc = UserCounter::get(UserCounter::TYPE_TALK_NEW_COMMENTS, $accountid, $talkid, false);
        	if($uc) {
        		$uc->value = 0;
        		$uc->save();
        	}        	
        }
        else {
        	$result = Comment::find(array('conditions'=>"roomid=? and (hide_flag=0 or (hide_flag=1 and accountid=?) ) $at_condition", 'page'=>$page['page'],'limit'=>$page['count'],'order'=>'id desc'), 
        			array($roomid, $accountid));
        }
        
        $items = array();
        foreach ( $result as $comment ) {
        	$profile = UserProfile::findByPk($comment->accountid);
        	$item = array('id' => $comment->id,'type'=>$comment->type,'accountid'=>$comment->accountid,'avatar'=>$profile->avatar,
        		'nickname'=>$profile->nickname,'voice'=>$comment->voice_array,'emotion'=>$comment->emotion,
        		'at_text'=>$comment->at_text,
        		'created'=>Utility::day3_to_date($comment->created));
        	$items[] = $item;
        }

        $data = array('items'=>$items);
        $data['is_last_page'] = count($result) < $page['count'] ? 1:0;
        
//         udebug(100007, $data);
        
        $this->success($data);
    }
    
    /**
     * 删除评论
     * @param id int 评论的ID
     * @return
     */
    public function www_destroy () {
    	$accountid = Auth::user('accountid');
        $id = $this->_getParam('id', '', true);
        if (empty($id) || !is_numeric($id) ) {
            $this->failed(Err::$INPUT_INVALID);
        } 
        
        $comment = Comment::findByPk($id); /* @var $comment Comment */
        if (!$comment) {
        	$this->failed(Err::$DATA_NOT_FOUND);
        }
        
        $room = Room::findByPk($comment->roomid);
        
        if ($accountid == $comment->accountid) {
        	$hide_by = ApiConst::HIDE_BY_SELF;
        }
        else if($accountid == $room->accountid) {
        	$hide_by = ApiConst::HIDE_BY_ROOM_OWNER;
        }
        else {
        	$this->failed(Err::$OPERATE_NOT_PERMIT);
        }
        
        $comment->hide(ApiConst::HIDE_FLAG_OTHERS, $hide_by);
        $this->success();
    }
    
    /**
     * 创建评论
     *
     * @return
     */
    public function www_create () {
    	$accountid = Auth::user('accountid');
        //获得房间ID,话题ID,语音时间
        $roomid = $this->_getParam('roomid', '', true);
        $talkid = $this->_getParam('talkid', '', true);
        $voice_time = $this->_getParam('voice_time', '', true);
        $emotion = $this->_getParam('emotion',0,true);
        $at_type = $this->_getParam('at_type',ApiConst::AT_TYPE_COMMENT,true);
        $at = $this->_getParam('at',2,true);
        
        if(!$roomid)
        	$this->failed(Err::$INPUT_REQUIRED);

        // 创建评论
        $comment = new Comment();
        $comment->accountid = $accountid;
        $comment->roomid = $roomid;
        $comment->talkid = $talkid;
        $comment->at_type = $at_type;
        $comment->at = $at;
        $comment->emotion = $emotion;
        
        if ($emotion <= 0) {
   	        //语音文件
	        $voice = $this->Uploader->uploadVoice($this->request->params['form']['voice'], $voice_time);
	        $voice_image = $this->Uploader->uploadImage($this->request->params['form']['voice_image']);
	        if($emotion ==0 && $voice === false) 
	        	$this->failed(Err::$UPLOAD_FAILED);
	        $comment->voice = $voice;
	        $comment->voice_time = $voice_time;
	        $comment->voice_image = $voice_image;
        }
        
        if ($at_type == ApiConst::AT_TYPE_TALK) {
        	$talk = Talk::findByPk($talkid);
        	if(!$talk)
        		$this->failed(Err::$DATA_NOT_FOUND);
        	$p2 = UserProfile::findByPk($talk->accountid);
        	$comment->at_text = '第'.$talk->floor.'席'.$p2->nickname.'的节目';
        }
        else if ($at_type == ApiConst::AT_TYPE_COMMENT) {
        	$comment2 = Comment::findByPk($comment->at);
        	$p2 = UserProfile::findByPk($comment2->accountid);
        	$comment->at_text = $p2->nickname;
        } 

        if(!$comment->save())
        	$this->failed(Err::$DATA_SAVE_ERROR);

        $profile = UserProfile::findByPk($accountid);
        $data = array('id' => $comment->id,'type'=>$comment->type,'accountid'=>$comment->accountid,
        		'nickname'=>$profile->nickname,'voice'=>$comment->voice_array,'emotion'=>$comment->emotion,
        		'at_text'=>$comment->at_text,
        		'created'=>Utility::day3_to_date($comment->created));
    	$this->success($data);    
    }    
}