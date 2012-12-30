<?php
/**
 * 用户私信
 * @property int $id
 * @property int $fromid
 * @property int $from_flag
 * @property int $toid
 * @property int $to_flag
 * @property int $send_flag
 * @property string $message
 * @property string $voice
 * @property int $voice_time
 * @property string $created
 */
class Message extends Model {
    public static $useTable = 'messages';
    public static $useDbConfig = 'message';
    
    const TO_FLAG_NONE 		= 0;
    const TO_FLAG_READ 		= 1;
    const TO_FLAG_DELETED 	= 2;
    
    const FROM_FLAG_NONE 	= 0;
    const FROM_FLAG_DELETED = 2;
    
    const SEND_FLAG_NONE	= 0;
    const SEND_FLAG_SPEAKER	= 1;

    public function read() {
    	if ($this->to_flag == self::TO_FLAG_NONE) {
    		$this->to_flag = self::TO_FLAG_READ;
    		$this->save();
    	}
    }
    
    /**
     * 删除私信 软删除
     * 1.可以删除所有的本人私信.2.可以删除某人给本人发的私信.3.可以删除某一个私信
     * @param int $accountid 当前登录人
     * @param int $type 私信的类型
     * @param int $value 私信的ID
     * @return array(	
     *                  code	=> int,                 //状态代码 正确与否
     *                  msg		=> string               //提示信息
     *               )
     */
    public static function remove($accountid, $type, $value) {
       switch ( $type ) {
           case ApiConst::DESTROY_PRIVATE_TYPE_ALL : //删除所有本人的私信
               $result_1 = self::update_all(array('to_flag' => '2'), "toid=? and to_flag != 2", array($accountid));
               $result_2 = self::update_all(array('from_flag' => '2'), "fromid=? and from_flag != 2", array($accountid));
               if ( true == $result_1 && true == $result_2 ) {
                   $result = Err::$SUCCESS;
               } else {
                   $result = Err::$FAILED;
               }
               break;
           case ApiConst::DESTROY_PRIVATE_TYPE_ACCOUNT_ID : //删除所有XX人发给 本人的私信
               $result_1 = self::update_all(array('to_flag' => '2'), "fromid=? and toid=? and to_flag != 2", array($value, $accountid));
               $result_2 = self::update_all(array('from_flag' => '2'), "fromid=? and toid=? and from_flag != 2", array($accountid, $value));
               if ( true == $result_1 && true == $result_2 ) {
                   $result = Err::$SUCCESS;
               } else {
                   $result = Err::$FAILED;
               }
               break;
           case ApiConst::DESTROY_PRIVATE_TYPE_ID : //删除一条私信
               //根据ID去数据库查找这条信息
               $message = self::findByPk($value);
               //如果当前用户是发信人 如果不是就看是不是 收件人
               if ( $accountid == $message->fromid ) {
                    //如果已经被软删除了
                    if ( 2 == $message->from_flag ) {
                        $result = Err::$SUCCESS;
                    } else {
                        //如果还没被软删除
                        $message->from_flag = 2;
                        $result = $message->save();
                        if ( true == $result ) {
                            $result = Err::$SUCCESS;
                        } else {
                            $result = Err::$FAILED;
                        }
                    }
                } elseif ( $accountid == $message->toid ) {
                    //如果已经被软删除了
                    if ( 2 == $message->to_flag ) {
                        $result = Err::$SUCCESS;
                    } else {
                        //如果还没被软删除
                        $message->to_flag = 2;
                        $result = $message->save();
                        if ( true == $result ) {
                            $result = Err::$SUCCESS;
                        } else {
                            $result = Err::$FAILED;
                        }
                    }
                } else {
                    //如果该信息跟当前用户无关
                    $result = Err::$AUTH_FAILED;
                }
                break;
           default : $result = Err::$FAILED;
       }
       return $result;
    }
    
    protected function afterSave() {
    	if($this->is_new_record()) {    		
    		MessageStatus::get($this->toid)->new_private_message($this);
    		NewMessageCounter::incrPrivateMessageCount($this);
    		NewMessage::push_private($this);    		
    		
    		if (intval(Preference::get($this->toid, Preference::PUSH_PRIVATE_MSG)) == 1) {
    			Resque::enqueue(QueueNames::ALOHA, JobNames::APNS_PUSH, array('accountid'=>$this->toid, 'message'=>"您有新的悄悄话"));
    		}    		
    	}
    	    	
    }
    
}