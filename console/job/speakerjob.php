<?php
require_once __DIR__.'/job.php';
/**
 * 喇叭任务
 * 
 * 把喇叭私信投递给粉丝
 */
class SpeakerJob extends Job {
	public function perform()
	{
		Log::write("perform: ".date('Y-m-d H:i:s')." ".print_r($this->args, true)."\n", __CLASS__);
		$accountid = $this->args['accountid'];//$msg = array('accountid'=>$accountid,'bagitemid'=>$use_bag_item,'message'=>$message,'voice'=>$voice,'voice_time'=>$voice_time);
		$bagitemid = $this->args['bagitemid'];
		
		$bagitem = BagItem::findByPk($bagitemid); /* @var $bagitem BagItem */
		// get all fans 
		$fans = Follow::get_follower_ids($accountid);
		
		Log::write("deliver speaker message accountid=$accountid to followers=".implode(',', $fans), __CLASS__);
		foreach ($fans as $toid) {
			$msg = new Message();
			$msg->toid = $toid;
			$msg->fromid = $accountid;
			$msg->message = $this->args['message'];
			$msg->voice = $this->args['voice'];
			$msg->voice_time = $this->args['voice_time'];
			$msg->send_flag = Message::SEND_FLAG_SPEAKER;
			$msg->save();	
		}
	}
}