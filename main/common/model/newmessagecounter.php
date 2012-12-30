<?php
/**
 * 新消息计数
 * 
 * @property int $id
 * @property int $toid
 * @property int $fromid
 * @property int $objectid
 * @property int $type
 * @property int $sub_type
 * @property int $count
 * @property datetime $modified
 */

class NewMessageCounter extends Model {
    public static $useTable = 'new_message_counter';
    public static $useDbConfig = 'message';

    /**
     * 增加系统消息计数
     * @param SystemMessage $sm
     * @return NewMessageCounter
     */
    public static function incrSystemMessageCount(SystemMessage $sm) {
    	$nmc = NewMessageCounter::first(array('conditions'=>"toid=? and type=? and sub_type=? and fromid=?"), array($sm->toid, $sm->type, $sm->sub_type, $sm->fromid));
    	
    	if(!$nmc) {
    		$nmc = new NewMessageCounter(array('toid'=>$sm->toid,'type'=>$sm->type,'sub_type'=>$sm->sub_type,
    				'fromid'=>$sm->fromid,'count'=>0));
    	}
    	
    	$nmc->count++;
    	$nmc->save();
    	 
		return $nmc;   	 
    }
    
    public static function incrPrivateMessageCount(Message $msg) {
    	$npc = NewMessageCounter::first(array('conditions'=>"toid=? and type=? and fromid=?"), array($msg->toid, ApiConst::MESSAGE_TYPE_PRIVATE, $msg->fromid));
    	 
    	if(!$npc) {
    		$npc = new NewMessageCounter(array('toid'=>$msg->toid,'type'=> ApiConst::MESSAGE_TYPE_PRIVATE,
    				'fromid'=>$msg->fromid,'count'=>0));
    		// 同时生成一个反向计数
    		$x = self::getPrivateNewMessageCounter($msg->toid, $msg->fromid);
    		$x->save();
    	}
    	 
    	$npc->count++;
    	$npc->save();
    	
    	return $npc;
    }
    
    /**
     * 返回私信计数
     * @param int $fromid
     * @param int $toid
     * @return NewMessageCounter
     */
    public static function getPrivateNewMessageCounter($fromid, $toid) {
    	$nmc = NewMessageCounter::first(array('conditions' => "toid=? and fromid=? and type=?"), array($toid, $fromid, ApiConst::MESSAGE_TYPE_PRIVATE));   
    	if(!$nmc) {
    		$nmc = new NewMessageCounter(array('toid'=> $toid,'type'=> ApiConst::MESSAGE_TYPE_PRIVATE,
    				'fromid'=> $fromid,'count'=>0));
    	} 	
    	return $nmc;
    } 

}
