<?php
/**
 * 回调控制器
 * @property UploaderComponent $Uploader
 * @property ValidatorComponent $Validator
 * @property LoginComponent $Login
 */
class CallbackApiController extends ApiController {
	public $components = array('Uploader','Validator','Login');
	
    const STATE_LOGIN = 1;
    const STATE_BIND = 2;

    //sina weibo用户登录成功后回调本方法。本方法通知用户服务，并负责创建和客户端的会话
    public function www_weibo() {
    	$this->tLogin("WEIBO");
    }
    
    public function www_qq() {
    	$this->tLogin("QQ");
    }
    
    public function www_t_qq() {
		$this->tLogin("T_QQ");
    }
    
    public function www_renren() {
    	$this->tLogin("RENREN");
    }
    
    private function tLogin($platform) {
    	$uri = parse_url($_SERVER["REQUEST_URI"]);
    	$query = $uri['query'];
    	parse_str(urldecode($query), $query);
    	 
    	$requestId = $_COOKIE[REQUESTID_COOKIE];//请求id，第一次请求用户服务时，由用户服务生成
    	$bizType = $_COOKIE[BIZTYPE_COOKIE];//获得本次请求的类型   login：登录   bind：绑定
    	setcookie (REQUESTID_COOKIE, "", time() - 3600, '/');//删除cookie
    	setcookie (BIZTYPE_COOKIE, "", time() - 3600, '/');//删除cookie
    	
    	$data['requestId'] = $requestId;
    	$data['platform'] = $platform;
    	$data['parameters'] = $query;
    	
    	//向用户服务通知第三方平台已经验证通过，请求继续本地登录服务验证
    	if ($bizType == "login") //用户登录
    		$data['bind'] = false;
    	else if($bizType == "bind") //用户绑定
    		$data['bind'] = true;
    	 
    	$data = json_encode($data);
    	
		$result = UsersThirdApi::callback($data);
		Log::write($result, 'callback');
		
    	$responseData = json_decode($result, true);    	
    	if (empty($responseData))
    		throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
    	
    	if ($responseData['code'] == USER_SERVICE_SUCCESS) { //用户服务返回成功
    		if ($bizType == "login") { //用户登录    			
    			$clientData = $_COOKIE[CLIENT_DATA_COOKIE];
    			setcookie (CLIENT_DATA_COOKIE, "", time() - 3600, '/');//删除cookie

    			// 创建profile
    			$accountid = $responseData['data']['userId'];
    			$nickname = $responseData['data']['thirdFirstLoginCheck']['nickName'];
    			$gender = $responseData['data']['gender'];
    			$gender = $gender=="MALE"?1:0;
    			$avatar = $responseData['data']['headPortrait'];
    			
    			$profileData = compact('accountid', 'nickname', 'gender','avatar');
    			// 首次登陆时，创建profile,并要求设置昵称和头像
    			$nickname_exist = false;
    			if (UserProfile::isNicknameExist($nickname)) {
    				$nickname_exist = true;
    				unset($profileData['nickname']);
    			}    			
    			
    			$loginResponse = $this->Login->createProfileAndLogin($profileData);
    			$loginResponse['binding_platforms'] = $responseData['data']['bindPlatformList'];
   				$this->success($loginResponse);    			
       		}
    		else if ($bizType == "bind") {//用户绑定    			
    			$this->success();
    		}
    	}
    	else {//认证失败    		
    		$ret = array($responseData['code'], $responseData['message']);
    		return $this->_respond($ret, $responseData['data']);
    	}
    }
       
    protected function _respond($errMsg, $data = null, $popup = null) {
    	if (!isset($data))
           $data = '';
        if (!isset($popup))
           $popup = '';
    	$result = json_encode(array(
           'code' => $errMsg[0],
           'message' => $errMsg[1],
           'data' => $data,
           'popup' => $popup
        ));
        echo '<html><title>login</title><body><div id="resultData" style="display:none">';
        echo $result;
        echo '</div></body></html>';
    }
}