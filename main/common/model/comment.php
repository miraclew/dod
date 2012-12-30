<?php
/**
 * 房间评论
 * 
 * @property int $id
 * @property int $accountid
 * @property int $roomid
 * @property int $talkid
 * @property string $voice
 * @property int $voice_time
 * @property string $voice_fid
 * @property string $voice_image
 * @property int $emotion
 * @property int $at_type
 * @property int $at
 * @property string $at_text
 * @property int $hide_flag
 * @property int $hide_by
 * @property datetime $created
 * 
 * // virtual
 * @property int $type
 * @property array $voice_array 
 */
class Comment extends Model {
    public static $useTable = 'comments';
    public static $useDbConfig = 'room';

    /**
     * 获取评论列表
     * @param $roomid int 房间id
     * @param $talkid int 评论id
     * @param $accountid int 当前阅读人
     * @param $page int 页码
     * @param $count int 每页多少
     * 
     * @return array (
     *                comment Object 评论对象
     *                )
     */
    public static function _list($roomid, $talkid, $accountid, $page) {
		if ( null != $talkid ) {
            $conditions_type  = "comment.talkid = {$talkid}";
        } else {
            $conditions_type  = "comment.roomid = {$roomid} and comment.talkid is null";
        }
        
        $conditions_type .= " and ( comment.hide_flag = 0 or ( comment.hide_flag = 1 and comment.accountid = {$accountid} ) )";
        $data = self::pageQuery(array(
        		'conditions' => $conditions_type,
        		'order'=>'created desc',
                'fields' => array('u.accountid', 'u.nickname', 'u.level', 'u.avatar', 'comment.id', 'comment.voice','comment.voice_fid', 'comment.voice_time', 'comment.created','comment.voice_image'),
                'joins' => array(
                		array('type'  => 'left', 'alias' => 'u', 'table' => 'qyh_user.user_profiles', 'conditions' => "comment.accountid = u.accountid")
                ),
                ), $page);
        
        return $data;
    }
    
    public static function pageQuery($query, $page) {
    	$query = array_merge(array(
    			'page' => $page['page'],
    			'limit' => $page['count'],
    	),$query);
    
    	if(empty($query['conditions'])) {
    		$query['conditions'] = "1=1";
    	}
    
    	if(empty($query['order'])) {
    		$query['order'] = "id desc";
    	}
    
    	if($page['maxid'] !== false) {
    		$query['conditions'] .= " and comment.id<".$page['maxid'];
    	}
    	if($page['sinceid'] !== false) {
    		$query['conditions'] .= " and comment.id>".$page['sinceid'];
    	}
    	
    	return self::query($query);
    }
    
    public function hide($hide_flag, $hide_by=ApiConst::HIDE_BY_ADMIN) {
    	$this->hide_flag = $hide_flag;
    	$this->hide_by = $hide_by;
    	$this->save();
    }
    
    // virtual property
    public function get_type() {
    	return $this->emotion > 0? ApiConst::COMMENT_TYPE_EMOTION: ApiConst::COMMENT_TYPE_VOICE;
    }
    
    public function get_voice_array() {
    	return array('fid'=>$this->voice_fid,'duration'=>$this->voice_time,'voice'=>$this->voice,'image'=>$this->voice_image);
    }
    
    protected function afterSave() {
    	if($this->is_new_record()) {
    		if ($this->emotion <= 0) {
	    		$upload = new Upload();
	    		$upload->type = Upload::TYPE_COMMENT_VOICE;
	    		$upload->ftype = Upload::FTYPE_VOICE;
	    		$upload->accountid = $this->accountid;
	    		$upload->objectid = $this->id;
	    		//$upload->url = $this->voice;
	    		$upload->save();
	    			
	    		$this->assign_attribute('voice_fid', $upload->fid);
	    		$this->update(); // don't call save    			
    		}
    		
    		$this->afterCreate();
    	}
    }
    
    private function afterCreate() {
    	$mention = new Mention();
    	$mention->type = $this->at_type;    	 
    	
    	if ($this->at_type == ApiConst::AT_TYPE_TALK) {
    		$talk = Talk::findByPk($this->talkid); /* @var $talk Talk */
    		$toid = $talk->accountid;
    		$objectid = $this->talkid;
    		$uc = UserCounter::get(UserCounter::TYPE_TALK_NEW_COMMENTS, $toid, $objectid);
    		$uc->incr();
    			
    		if ($this->emotion > 0 && $toid != $this->accountid) {
    			$p1 = UserProfile::findByPk($this->accountid); /* @var $p1 UserProfile */    			
    			$p2 = UserProfile::findByPk($talk->accountid); /* @var $p2 UserProfile */
    			$p1->pop_value += 30;
    			$p2->pop_value += 30;
    			$p1->save();
    			$p2->save();
    			$talk->pop_value += 30;
    			$talk->save();
    		}
    		
    		
    	}
    	else if($this->at_type == ApiConst::AT_TYPE_COMMENT) {
    		$comment = Comment::findByPk($this->at);
    		$toid = $comment->accountid;
    		$objectid = $comment->id;
    	}
    	
    	// @
    	$mention->accountid = $toid;
    	$mention->friendid = $this->accountid;
    	$mention->objectid = $objectid;
    	$mention->commentid = $this->id;
    	$mention->roomid = $this->roomid;    	
    	$mention->save();
    	
    	$room = Room::findByPk($this->roomid);
    	$room->join($this->accountid);
    	
    	EventManager::instance()->dispatch(new Event(EventNames::COMMENT_CREATE, $this, array('comment'=>$this)));
    	
    	if ($this->accountid != $room->accountid) {
    		$sm = new SystemMessage();
   			$sm->toid = $toid;
   			$sm->fromid = $this->accountid;
   			$sm->type = ApiConst::MESSAGE_TYPE_COMMENT;
   			$sm->sub_type = $this->emotion > 0?ApiConst::MESSAGE_SUB_TYPE_COMMENT_EMOTION:ApiConst::MESSAGE_SUB_TYPE_COMMENT_VOICE;
   			$sm->objectid = $this->id;
   			$sm->annotations = json_encode(array('roomid'=>$this->roomid));
   			$sm->save();
    	
    		$ms = MessageStatus::get($toid);
    		if($ms->new_comment == 10) {
    			Resque::enqueue(QueueNames::ALOHA, JobNames::APNS_PUSH, array('accountid'=>$toid, 'message'=>"10条未读新评论"));
    		}
    	}
    	
		Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::COMMENT_CREATE,'accountid'=>$this->accountid,'data'=>array('id'=>$this->id)));
    }
}