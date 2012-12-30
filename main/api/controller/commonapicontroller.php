<?php

class CommonApiController extends ApiController {
    public function beforeFilter() {
        //提交通用信息请求，不需要认证
        return true;
    }
    
    public function www_version() {
        $data = array('openlogin' => array(
            array(
                'name' => 'wb',
                'url' => OpenApi::getLoginUrl('wb'),
            ),
            array(
                'name' => 'qq',
                'url' => OpenApi::getLoginUrl('qq'),
            ),
            array(
                'name' => 'rr',
                'url' => OpenApi::getLoginUrl('rr'),
            )
        ));
        $data['statesynctime'] = 30;        //定时获取服务器的用户状态的时间
        $this->_respond(Err::$SUCCESS, $data);
    }
    
    public function www_debug() {
    	//$profile = UserProfile::findByPk(11111,222);
    	//$profiles = UserProfile::last(array("conditions"=>'','fields'=>''), array());
    	//$data = UserProfile::query(array("conditions"=>'','fields'=>''), array());
    	//$data = UserProfile::queryBySql("sss");
    	
    	$t1 = microtime(true);
    	$profile = UserProfile::findByPk(114023);
    	debug(microtime(true)-$t1);
    	$t1 = microtime(true);
    	$profile = UserProfile::findByPk(114023);
    	debug(microtime(true)-$t1);
    }
    
    public function www_phpinfo() {
    	echo phpinfo();die;
    }
    
    public function www_version1() {
        $data = array('openlogin' => array(
            array(
                'name' => 'wb',
                'url' => OpenApi::getLoginUrl('wb'),
            ),
            array(
                'name' => 'qq',
                'url' => OpenApi::getLoginUrl('qq'),
            ),
            array(
                'name' => 'rr',
                'url' => OpenApi::getLoginUrl('rr'),
            )
        ));
        $data['statesynctime'] = 30;        //定时获取服务器的用户状态的时间
        $this->_respond(Err::$SUCCESS, $data);
    }
    
    public function www_test() {
    	Cache::write('test', '1234');
    	debug(Cache::read(array('test','test2')));
    }
    
    public function www_setupdata() {
        $module = $this->_getParam('module', 'questions');
        $sh = '/var/www/html/venus/console/shell initdata ' . $module;
        $result = exec($sh);
        $this->_respond(Err::$SUCCESS, $result);
    }
    
    public function www_finishquestion() {
        $questionId = $this->_getParam('questionid', 0);
        $sh = '/var/www/html/venus/console/shell initdata finishquestion ' . $questionId;
        $result = exec($sh);
        $this->_respond(Err::$SUCCESS, $result);
    }
    
    
    public function www_messageque() {
    	/*Cache::write('test', 'teste123', 1000, 'redis');
    	echo Cache::read('test', 'redis');
    	
    	return;*/
    	$key = 'test';
    	/*$value='{
				"userId":105000,
				"platformList":[
				"RENREN"
				],
				"message":"测试消息21!"
				}';*/
    	$value = 'aadfasd333aa';
    	$duration = 1000;
    	/*$redis = new Redis();
    	$redis->connect('192.168.1.135',6379);
    	$redis->rPush($key, $value);*/
		//$redis = Cache::getRedis('default1');
    	//$redis->rPush($key, $value);
    	//debug($redis->rPop($key));
    	
    	MessageQueue::instance()->writeList($key, $value, $duration, 'messagequeue');
    	$t = MessageQueue::instance()->readList($key, 'messagequeue');
    	//MessageQueue::instance()->write($key, $value, $duration, 'messagequeue');
    	//$t = MessageQueue::instance()->read($key, 'messagequeue');
    	debug($t);

    }
    
    public function www_release() {
        $file = $this->request->params['form']['releasefile'];
        if (empty($file))
            return $this->_respond(Err::$FAIL);

        if ($file['error'] == 0 && is_uploaded_file($file['tmp_name'])) {
            $realPath = PUBLIC_.'release/'.$file['name'];
            if (move_uploaded_file($file['tmp_name'], $realPath)) {
                return $this->_respond(Err::$SUCCESS);
            }
        }
        return $this->_respond(Err::$FAIL);
    } 
}