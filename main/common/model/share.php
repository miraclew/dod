<?php
/**
 * 用户分享(转发)
 * 
 * @property int $id
 * @property int $accountid
 * @property int $ownerid
 * @property int $type
 * @property int $objectid
 * @property string $platform
 * @property datetime $created
 */
class Share extends Model {
	public static $useTable = 'shares';
	public static $useDbConfig = 'user';
	
	const TYPE_ROOM = 1;
	const TYPE_TALK = 2;
	
	public function get_platform_name() {
		$names = array('WEIBO'=>'新浪微博','T_QQ'=>'腾讯微博','WECHAT'=>'微信','QQ'=>'腾讯QQ');
		return $names[$this->platform];
	}
	
	protected function afterSave() {
		if ($this->is_new_record()) {
			$this->afterCreate();
		}
	}
	
	private function afterCreate() {
		if ($this->type == self::TYPE_TALK) {
			$talk = Talk::findByPk($this->objectid);
			
			$count = Counter::get(Counter::TYPE_TALK_SHARE, $talk->id)->incr();
		
			$task = Task::findByPk(Task::ID13_DAILY_SHARE); /* @var $task Task */
			$task->accomplish($talk->accountid);
			
			Resque::enqueue(QueueNames::ALOHA, JobNames::FANOUT, array('type'=>FanoutType::SHARE_CREATE,'accountid'=>$this->accountid,'data'=>array('id'=>$this->id)));
			
			$new_records = array(10, 100, 500, 1000);
			foreach ($new_records as $value) {
				if($count == $value) {
// 					// 发送系统消息
// 					$toid = $talk->accountid;
// 					$sm = new SystemMessage();
// 					$sm->type = ApiConst::MESSAGE_TYPE_ROOM;
// 					$sm->sub_type = ApiConst::MESSAGE_SUB_TYPE_ROOM_TALK_SHARE;
// 					$sm->toid = $toid;
// 					$sm->fromid = $talk->accountid;
// 					$sm->objectid = $talk->id;
// 					$sm->annotations = json_encode(array('shares'=>$count));
// 					$sm->save();
				}
			}			
		}		
	}
}