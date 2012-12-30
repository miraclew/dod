<?php
class InterviewApiController extends ApiController {
    
    /**
     * 显示所有采访（问和答)
     * 先判断 是否输入了用户了ID 如果没有就是查询当前的用户ID. 查询他们的所有回答的信息
     */
    public function www_list() {
        //输入的用户ID号
        $accountid = $this->_getParam('accountid');
        //如果没有输入就找当前登录的用户ID
        if ( null == $this->_getParam('accountid') ) {
            //获取当前用户的id
            $accountid = Auth::user('accountid');
        }
        //数据库查询出当前用户的所有 回答的信息
        $result = Interview::find(array('conditions'=>"answer_accountid=?"), array($accountid));
        if ( false == is_array($result) ) {
            $this->_respond(Err::$FAILED);
        } else {
            $data                   = array();
            foreach ($result as $value) {
                $data['items'][]    = $value->attributes();
            }
            $this->_respond(Err::$SUCCESS, $data);
        }
    }
    
    /**
     * 提问
     * 先判断 是否输入了用户了ID 如果没有就是查询当前的用户ID. 查询他们的所有回答的信息
     */
    public function www_ask() {
        //提问人
        $fromid = Auth::user('accountid');
        //像谁提问
        $toid = $this->_getParam('accountid');
        //获得语音时长
        $voice_time = $this->_getParam('voice_time', 0, true);
        //语音文件
        $voice = $this->Uploader->uploadVoice($this->request->params['form']['voice'], $voice_time);
        if ( true == empty($accountid) || true == empty($toid) || !is_numeric($fromid) || !is_numeric($toid) || true == empty($voice_time) || true == empty($voice) ) {
            $this->_respond(Err::$INPUT_FORMAT_INVALID);
        } else {
            //添加提问
            $result = Interview::_ask($fromid, $toid, $voice, $voice_time);
            
        }
    }
    
    public function www_show() {
        echo __FUNCTION__;
    }
    
    public function www_create() {
        echo __FUNCTION__;
    }
    
    public function www_destroy() {
        echo __FUNCTION__;
    }    
    
}