<?php
/**
 * 道具使用
 *
 * @property int $id
 * @property int $from_accountid
 * @property int $to_accountid
 * @property int $type
 * @property int $itemid
 * @property int $quantity
 * @property int $total_quantity
 * @property int $roomid
 * @property int $talkid
 * @property int $status
 * @property datetime $created
 */
class ItemUsing extends Model {
    public static $useTable = 'item_using';
    public static $useDbConfig = 'store';
    
    // 使用类型
    const TYPE_ROOM = 1; // 房间 
    const TYPE_TALK = 2; // 创作
    const TYPE_USER = 3; // 直接送人   
    
    const STATUS_NORMAL = 1;
    const STATUS_THANK 	= 2; 
    
    protected function afterSave() {
    	if($this->is_new_record()) {
    		$this->afterCreate();
    		EventManager::instance()->dispatch(new Event(EventNames::ITEM_USING,$this, array('itemusing'=>$this)));
    	}
    }
    
    private function afterCreate() {
    	$item = Item::findByPk($this->itemid); /* @var $item Item */
    	$p1_add = $item->p1_add * $this->quantity; // 赠送者
    	$p2_add = $item->p2_add * $this->quantity; // 收礼者
    	
    	$p1 = UserProfile::findByPk($this->from_accountid); /* @var $p1 UserProfile */
    	$p1->add_pop_value($p1_add);
    	
    	$p2 = UserProfile::findByPk($this->to_accountid); /* @var $p2 UserProfile */
    	$p2_add = $p2->add_pop_value($p2_add);
    	
    	if($this->type == ItemUsing::TYPE_ROOM) {
    		$room = Room::findByPk($this->roomid); /* @var $room Room */
    		$room->pop_value += $p2_add;
    		$room->save();
    			
    		SystemRankings::instance()->addRoom($room->id, $room->like_count);
    	}
    	elseif($this->type == ItemUsing::TYPE_TALK) {
    			
    		$talk = Talk::findByPk($this->talkid); /* @var $talk Talk */
    		$talk->pop_value += $p2_add;
    		$talk->save();
    		$fromid = UserProfile::findByPk($this->from_accountid);
    		UserCounter::get(UserCounter::TYPE_USER_ROOM_POP, $this->to_accountid, $this->roomid)->incr($p2_add);
    	
    		$new_message = $fromid->nickname.'赠送'.$talk->floor.'席的'.$p2->nickname.'玫瑰x'.$this->total_quantity.'。';
    		RoomNewMessages::get($this->roomid)->add($new_message);
    	}
    	else if($this->type == ItemUsing::TYPE_USER) {
    	}
    	else
    		return ;
    	
    	// 统计
    	$is = InteractionStat::get(InteractionStat::STAT_TYPE_POP_CONTRIBUTE, $this->from_accountid, $this->to_accountid);
    	$is->value += $p2_add;
    	$is->save();
    	
    	// 任务
    	$task = Task::findByPk(Task::ID8_FIRST_GIVE_GIFT); /* @var $task Task */
    	$task->accomplish($this->from_accountid);
    	$task2 = Task::findByPk(Task::ID11_DAILY_GIVE_GIFT); /* @var $task2 Task */
    	$task2->accomplish($this->from_accountid);
    	
    	// 发送系统消息
//     	$sm = new SystemMessage();
//     	$sm->type = ApiConst::MESSAGE_TYPE_COMMUNITY;
//     	$sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_COMMUNITY_NEW_GIFT;
//     	$sm->toid = $this->to_accountid;
//     	$sm->fromid = $this->from_accountid;
//     	$sm->objectid = $this->id;
//     	$sm->annotations = json_encode(array('roomid' => $this->roomid));
//     	$sm->save();
    }
}