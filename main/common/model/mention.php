<?php
/**
 * 提及@
 *
 * @property int $id
 * @property int $accountid 被@的人
 * @property int $friendid
 * @property int $type // ApiConst::AT_TYPE
 * @property int $roomid
 * @property int $title
 * @property int $commentid
 * @property int $objectid
 * @property datetime $created
 * 
 */

class Mention extends Model {
    public static $useTable = 'mentions';
    public static $useDbConfig = 'message';

    
    protected function afterSave() {
    	if ($this->is_new_record()) {
    		$this->pushMessage();
    	}
    }
    
    private function pushMessage() {
    	if($this->accountid != $this->friendid) {
    		$profile = UserProfile::findByPk($this->friendid);
    		$room = Room::findByPk($this->roomid);
    		if ($this->type == ApiConst::AT_TYPE_COMMENT) {
    			$comment = Comment::findByPk($this->objectid);
	    		$c = $comment->emotion>0?"心情":"评论"; 
	    		$message = $profile->nickname."在".$room->title."派对中@了您的 $c";
	    		Resque::enqueue(QueueNames::ALOHA, JobNames::APNS_PUSH, array('accountid'=>$this->accountid, 'message'=> $this->message));
    		}
    	}
    }
}