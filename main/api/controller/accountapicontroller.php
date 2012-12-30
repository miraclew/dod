<?php

/**
 * 账户控制器
 * @property UploaderComponent $Uploader
 * @property ValidatorComponent $Validator
 * @property LoginComponent $Login
 */
class AccountApiController extends ApiController {
	public $components = array('Uploader','Validator','Login');
	
	const LOGIN_ON = 1;//1：本服务校验登录  2：向认证服务登录   
	const AUTH_LOGIN_OK = 0;//认证平台认证成功	
	
	//注册
	public function www_register() {
		//获取输入参数
		$appId = $this->_getParam('appid');
		$channelId = $this->_getParam('channelid');
		$equipmentId = $this->_getParam('equipmentid');
		$applicationVersion = $this->_getParam('applicationversion');
		$systemVersion = $this->_getParam('systemversion');
		$cellBrand = $this->_getParam('cellbrand');
		$cellModel = $this->_getParam('cellmodel');
		$name = $this->_getParam('name');
		$password = $this->_getParam('password');
		$nickName = $this->_getParam('nickname', '');
		$genderParam = $this->_getParam('gender', 1, true);
		if(empty($genderParam)) $genderParam = 1;
		$gender = ($genderParam == 1) ? 'MALE' : 'FEMALE';
		//校验输入的合法性，并判断昵称是否重复
		$this->Validator->validateUserId($name);
		$this->Validator->validatePassword($password);
		$this->Validator->validateNickName($nickName);
		$ipAddress = $this->request->clientIp();
		
		$requestData = compact('appId', 'channelId',"equipmentId", "applicationVersion",'systemVersion', "cellBrand", "cellModel", "ipAddress",'name', "password", 'nickName', "gender");
		
		$apiRequest = $requestData;
		$apiRequest['gender'] = $apiRequest['gender'] == 1?'MALE' : 'FEMALE';
		$apiResponse = UsersApi::registerUser(json_encode($apiRequest));
		unset($requestData['password']);
		$apiResult = json_decode($apiResponse, true);
		
		
		if (empty($apiResult) || !isset($apiResult['code'])) {
			Log::write(__FUNCTION__." UsersApi::registerUser failed with params: ".json_encode($requestData), 'error');
			throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
		}
		else if ($apiResult['code'] != USER_SERVICE_SUCCESS) { //注册失败
			Log::write("register:userservice error: request= ".json_encode($requestData).",response=".$apiResponse, 'error');
			$ret = array($apiResult['code'], $apiResult['message']);
			return $this->_respond($ret);
		}
		else { //注册成功 ，开始建立会话，并返回应答数据
			$accountId = $apiResult['data']['userId'];
			if (UserProfile::exsit("accountid=$accountId")) {	//用户已经存在
				Log::write(__FUNCTION__." UsersApi::registerUser: accountid alread exist:$accountId: ".json_encode($requestData), 'error');
				throw new ErrRtnException(Err::$REG_USERID_EXIST);
			}
			else {
				$birthday = "1980-01-01";
				$rand = 'p/' . mt_rand(1, 12);
				$randAvatar = HTTP_PATH . $rand . '.jpg';
				
				$profileData = array('accountid' => $accountId, 'nickname' => $nickName, 'gender' => $genderParam,'avatar'=>$randAvatar, 'level' => 1, 'vip' => 0, 'points' => UserProfile::INITIAL_POINTS, 'birthday' => $birthday);
				$profile = new UserProfile($profileData);
				if(!$profile->save()) {
					Log::write("REG_CREATE_PROFILE_FAIL: ".json_encode($profileData), 'error');
					throw new ErrRtnException(Err::$REG_CREATE_PROFILE_FAIL);
				}
				
				EventManager::instance()->dispatch(new Event(EventNames::USER_REGISTER, $this, array('accountid'=>$accountId, 'profile'=>$profile)));
				// auto login after register
				$loginResponse = $this->Login->login($profile, $applicationVersion);
				$this->_respond(Err::$SUCCESS, $loginResponse);
			}
		}
	}
	
	public function www_login() {
		global $dlConfig;
		$appId = $this->_getParam('appid');
		$channelId = $this->_getParam('channelid');
		$equipmentId = $this->_getParam('equipmentid');
		$applicationVersion = $this->_getParam('applicationversion');
		$systemVersion = $this->_getParam('systemversion');
		$cellBrand = $this->_getParam('cellbrand');
		$cellModel = $this->_getParam('cellmodel');
		$name = $this->_getParam('name');
		$password = $this->_getParam('password');
		
		$ipAddress = $this->request->clientIp();
		
		$requestData = compact('appId', 'channelId',"equipmentId", "applicationVersion",'systemVersion', "cellBrand", "cellModel", "ipAddress",'name', "password");
		$requestData['appSecret'] = APP_SECRET;		
		
		$apiRequest = $requestData;
		$apiResponse = UsersApi::login(json_encode($apiRequest));
		unset($requestData['password']);
		$apiResult = json_decode($apiResponse, true);
// 		$apiResponse = "aaa";
// 		$apiResult = array('code' => 0, 'data'=>array('userId'=>101017,'bindPlatformList'=>array('T_QQ')));
		
		if (empty($apiResponse)) {
			Log::write(__FUNCTION__." UsersApi::login failed with params: ".json_encode($requestData), 'error');
			return $this->_respond(Err::$LOGIN_TIMEOUT);
		}
		else if(empty($apiResult) || !isset($apiResult['code'])) {
			Log::write(__FUNCTION__." UsersApi::login failed with params: ".json_encode($requestData), 'error');
			throw new ErrRtnException(Err::$USER_SERVICE_INVALID);				
		}
		else if($apiResult['code'] != USER_SERVICE_SUCCESS) { //认证失败			
			Log::write(__FUNCTION__." failed: request= ".json_encode($requestData).",response=".$apiResponse, 'error');
			$ret = array($apiResult['code'], $apiResult['message']);
			return $this->_respond($ret);
		}
		else { 
			$accountId = $apiResult['data']['userId'];
			$user = UserProfile::findByPk($accountId);
			if ( false == $user ) {
			    $user = new UserProfile();
			    $user->accountid = $apiResult['data']['userId'];
			    $user->nickName = $apiResult['data']['nickName'];
			    if ( 'MALE' == $apiResult['data']['gender'] ) {
			        $user->gender = 0;
			    } else {
			        $user->gender = 1;
			    }
			    if ( 'NORMAL' == $apiResult['data']['status'] ) {
			        $user->status = 1;
			    } else {
			        $user->status = 2;
			    }
			    
			    $user->save();
			}
			$loginResponse = $this->Login->login($user,  $applicationVersion);
			$loginResponse['binding_platforms'] = $apiResult['data']['bindPlatformList'];
			
			$login = new LoginLog();
			$login->accountid = $user->accountid;
			$login->type = LoginLog::LOG_TYPE_1;
			//$login->udid = $channelId;
			$login->channelId = $channelId;
			$login->equipmentid = $equipmentId;
			$login->applicationversion = $applicationVersion;
			$login->cellbrand = $cellBrand;
			$login->cellModel = $cellModel;
			$login->systemversion = $systemVersion;
			$login->ip = $ipAddress;
			$login->save();
			
			$this->success($loginResponse);				
		}
	}
	
	public function www_auto_login() {
		Log::writeDebug("autologin");
		
		global $dlConfig;
		$appId = trim($this->_getParam('appid'));
		$channelId = trim($this->_getParam('channelid'));
		$equipmentId = trim($this->_getParam('equipmentid'));
		$applicationVersion = trim($this->_getParam('applicationversion'));
		$systemVersion = trim($this->_getParam('systemversion'));
		$cellBrand = trim($this->_getParam('cellbrand'));
		$cellModel = trim($this->_getParam('cellmodel'));
		$clientIp = $this->request->clientIp();		
		
		Log::writeDebug("autologin cookie: ". isset($_COOKIE["ndd"])?$_COOKIE["ndd"]:'');
		
		$loginResponse = $this->Login->autoLogin(array('applicationversion' => $applicationVersion));
		$accountid = $loginResponse['accountid'];	
			
		// call api
		$result = UsersApi::getUser(json_encode(array('appId' => $appId, 'userId' => $accountid)));
		$responseData = json_decode($result, true);
		if ($responseData['code'] != USER_SERVICE_SUCCESS) { //认证成功 ，开始建立会话，并返回应答数据			
			$this->failed(Err::$AUTH_FAILED);			
		}
		$loginResponse['binding_platforms'] = $responseData['data']['bindPlatformList'];
		
		$login = new LoginLog();
		$login->accountid = $accountid;
		$login->type = LoginLog::LOG_TYPE_1;
		$login->channelId = $channelId;
		$login->equipmentid = $equipmentId;
		$login->applicationversion = $applicationVersion;
		$login->cellbrand = $cellBrand;
		$login->cellModel = $cellModel;
		$login->systemversion = $systemVersion;
		$login->ip = $clientIp;
		$login->save();		
		
		$this->success($loginResponse);
	}
	
	public function www_logout() {
		$accountId = Auth::user('accountid');
		if (!empty($accountId))
			$this->Login->logout();
		
		$this->_respond(Err::$SUCCESS);
	}
	 
	public function www_profile() {
		$accountId = Auth::user('accountid');
		
		$profile = UserProfile::findByPk($accountId); /* @var $profile UserProfile */		
		if ($profile) {
			$data = Utility::arrayExtract($profile->attributes(), array('nickname','avatar','navtive_place','occupation'));
			$data['birthday'] = $profile->birthday;
			$data['gender'] = $profile->gender;
			$data['dialect'] = $profile->dialect;
			$data['introduction'] = $profile->introduction;
			$data['bg_image_id'] = $profile->bg_image_id;
			$this->success($data);
		}
		else {
			$this->failed(Err::$USER_PROFILE_NOTEXIST);
		}
	}
	
	//第三方平台账户登录
	public function www_t_login() {
		$loginParam = trim($this->_getParam('loginParam'));
		$loginParam = json_decode($loginParam, true);
		$appId = $loginParam['appid'];
		$channelId = $loginParam['channelid'];
		$equipmentId = $loginParam['equipmentid'];
		$applicationVersion = $loginParam['applicationversion'];
		$systemVersion = $loginParam['systemversion'];
		$cellBrand = $loginParam['cellbrand'];
		$cellModel = $loginParam['cellmodel'];
		$platform = $loginParam['platform'];//WEIBO  RENREN  T_QQ QQ
		//debug($loginParam);
		$clientIp = $this->request->clientIp();
	
		if ($platform == 'WEIBO')
			$callbackUrl = CALLBACK_URL . "/weibo";
		else if ($platform == 'RENREN')
			$callbackUrl = CALLBACK_URL . "/renren";
		else if ($platform == 'T_QQ')
			$callbackUrl = CALLBACK_URL . "/t_qq";
		else if ($platform == 'QQ')
			$callbackUrl = CALLBACK_URL . "/qq";
		else{
			$dataLog = array('appId' => $appId, "channelId"=>$channelId,"equipmentId"=>$equipmentId, "applicationVersion"=>$applicationVersion,
					'systemVersion' => $systemVersion, "cellBrand"=>$cellBrand, "cellModel"=>$cellModel,	"ipAddress" => $clientIp
			);
			$dataLog = json_encode($dataLog);
			Log::write("51:$dataLog", 'login3failure');
			return $this->_respond(Err::$INPUT_INVALID);
		}
		
		Session::instance()->write("TLogin.ApplicationVersion", $applicationVersion);		
		
		Log::writeDebug("login write session applicationversion=$applicationVersion");
		
		//向用户服务申请第三方平台账户登录
		$clientIp = $this->request->clientIp();
		$data = array('appId' => $appId, "channelId"=>$channelId,"equipmentId"=>$equipmentId, "applicationVersion"=>$applicationVersion,
				'systemVersion' => $systemVersion, "cellBrand"=>$cellBrand, "cellModel"=>$cellModel,	"ipAddress" => $clientIp,
				'platform' => $platform, 'callbackUrl' => $callbackUrl);
		$plainData = $data;
		$data = json_encode($data);
		$result = UsersThirdApi::login($data);
		//debug($result); 
		$responseData = json_decode($result, true);
		if (empty($responseData)) {
			$dataLog = array('appId' => $appId, "channelId"=>$channelId,"equipmentId"=>$equipmentId, "applicationVersion"=>$applicationVersion,
					'systemVersion' => $systemVersion, "cellBrand"=>$cellBrand, "cellModel"=>$cellModel,	"ipAddress" => $clientIp
			);
			$dataLog = json_encode($dataLog);
			Log::write("53:$dataLog", 'login3failure');
			throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
	
		}
		if ($responseData['code'] == USER_SERVICE_SUCCESS) { //成功
			$redirectUrl = $responseData['data']['redirectUrl'];
			$requestId = $responseData['data']['requestId'];
			setcookie(REQUESTID_COOKIE, $requestId, time()+3600*100, '/');
			setcookie(BIZTYPE_COOKIE, 'login', time()+3600*100, '/');//设置cookie，本次请求为第三方账户登录，有效期1小时
			setcookie(CLIENT_DATA_COOKIE, json_encode($plainData), time()+3600*100, '/');
			//通知客户端重定向到第三方平台登录页面
			header("Location: $redirectUrl");
			die;
		}
		else {//认证失败
			if ($responseData['code'] != 10500) {
				$ret = array($responseData['code'], $responseData['message']);
				$dataLog = array('appId' => $appId, "channelId"=>$channelId,"equipmentId"=>$equipmentId, "applicationVersion"=>$applicationVersion,
						'systemVersion' => $systemVersion, "cellBrand"=>$cellBrand, "cellModel"=>$cellModel,	"ipAddress" => $clientIp
				);
				$dataLog = json_encode($dataLog);
				Log::write("54:$dataLog:$result", 'login3failure');
				return $this->_respond($ret);
			}
			else { //未开通
				$dataLog = array('appId' => $appId, "channelId"=>$channelId,"equipmentId"=>$equipmentId, "applicationVersion"=>$applicationVersion,
						'systemVersion' => $systemVersion, "cellBrand"=>$cellBrand, "cellModel"=>$cellModel,	"ipAddress" => $clientIp
				);
				$dataLog = json_encode($dataLog);
				Log::write("55:$dataLog", 'login3failure');
				echo '<html><title>login</title><body><div id="result" style="display:block">';
				echo $responseData['message'];
				echo '</div></body></html>';
			}
			return ;
		}
	}
	 
	//第三方平台账户绑定
	public function www_t_bind() {
		$userId = Auth::user('accountid');
	
		$bindParam = trim($this->_getParam('bindParam'));
		$bindParam = json_decode($bindParam, true);
		$platform = $bindParam['platform'];//WEIBO  RENREN  T_QQ
		if ($platform == 'WEIBO')
			$callbackUrl = CALLBACK_URL . "/weibo";
		else if ($platform == 'RENREN')
			$callbackUrl = CALLBACK_URL . "/renren";
		else if ($platform == 'QQ')
			$callbackUrl = CALLBACK_URL . "/qq";
		else if ($platform == 'T_QQ')
			$callbackUrl = CALLBACK_URL . "/t_qq";
		else {
			Log::write("70:$userId:$bindParam:$platform\n", 'login3failure');
			return $this->_respond(Err::$Q_INVALID_PARAMETER);
		}
		 
	
		$data = array('userId' => $userId, 'platform' => $platform,
				'callbackUrl' => $callbackUrl, 'appId' => APP_ID);
		$data = json_encode($data);
		Log::write(__FUNCTION__." : UsersThirdApi::bind param: ".$data, 'account');
		$result = UsersThirdApi::bind($data);
		Log::write(__FUNCTION__." : UsersThirdApi::bind result: ".$result, 'account');
		 
		$responseData = json_decode($result, true);
		if (empty($responseData)) {
			Log::write("72:$data:$result", 'login3failure');
			throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
		}
		if ($responseData['code'] == USER_SERVICE_SUCCESS) { //成功
			$redirectUrl = $responseData['data']['redirectUrl'];
			$requestId = $responseData['data']['requestId'];
			setcookie(REQUESTID_COOKIE, $requestId, time()+3600*100, '/');
			setcookie(BIZTYPE_COOKIE, 'bind', time()+3600*100, '/');//设置cookie，本次请求为第三方账户登录，有效期1小时
	
			//通知客户端重定向到第三方平台登录页面
			header("Location: $redirectUrl");
			die;
		}
		else {//认证失败
			if ($responseData['code'] != 10500) {
				Log::write("73:$data:$result", 'login3failure');
				$ret = array($responseData['code'], $responseData['message']);
				return $this->_respond($ret);
			}
			else { //未开通
				Log::write("74:$data:$result", 'login3failure');
				echo '<html><title>login</title><body><div id="result" style="display:none">';
				echo $responseData['message'];
				echo '</div></body></html>';
			}
			return ;
		}
	}
	
	//第三方平台账户解除绑定
	public function www_t_unbind() {
		$userId = Auth::user('accountid');
		$platform = trim($this->_getParam('platform'));//WEIBO  RENREN  T_QQ
		if (!in_array($platform, array('WEIBO', 'RENREN', 'T_QQ', 'QQ')))
			return $this->_respond(Err::$Q_INVALID_PARAMETER);
	
		//向用户服务申请第三方平台账户登录
		$data = array('userId' => $userId,
				'platform' => $platform, 'appId' => APP_ID);
		$data = json_encode($data);
		$result = UsersThirdApi::unbind($data);
		 
		$responseData = json_decode($result, true);
		if (empty($responseData))
			throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
		if ($responseData['code'] == USER_SERVICE_SUCCESS) { //成功
			$this->_respond(Err::$SUCCESS);
	
		}
		else {//认证失败
			$ret = array($responseData['code'], $responseData['message']);
			return $this->_respond($ret);
		}
	}
	
	public function www_t_init_nickname() {
		$requestId = trim($this->_getParam('requestid'));
		$platform = trim($this->_getParam('platform'));//WEIBO  RENREN  T_QQ
		$nickName = trim($this->_getParam('nickname'));
		$applicationVersion = trim($this->_getParam('applicationversion'));

		if (!Validator::isNickNameValid($nickName)) {
			$this->failed(Err::$ACCOUNT_NICKNAME_INVALID);
		}
		
		if (UserProfile::isNicknameExist($nickName)) {
			$this->failed(Err::$ACCOUNT_NICKNAME_EXIST);
		}
		
		$accountid = Auth::user('accountid');
		$profile = UserProfile::findByPk($accountid);
		$profile->nickname = $nickName;
		$profile->save();
		
		$this->success();
	}
	
	//修改昵称，第三方账户登录时如果昵称重复，则客户端需调用该接口，做修改昵称操作，成功后才能创建账户
	public function www_t_init_nickname_deprecated() {
		$requestId = trim($this->_getParam('requestid'));
		$platform = trim($this->_getParam('platform'));//WEIBO  RENREN  T_QQ
		$nickName = trim($this->_getParam('nickname'));
		$applicationVersion = trim($this->_getParam('applicationversion'));
		$this->Validator->validateNickName($nickName, true);
		 
		if (!in_array($platform, array('WEIBO', 'RENREN', 'T_QQ')))
			return $this->_respond(Err::$INPUT_INVALID);
	
		$data = array('requestId' => $requestId, "nickName"=>$nickName,
				'platform' => $platform);
		$data = json_encode($data);
		$result = UsersThirdApi::changeNickName($data);
		Log::writeDebug($result);
		$responseData = json_decode($result, true);
		
		if (empty($responseData))
			throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
		if ($responseData['code'] == USER_SERVICE_SUCCESS) { //成功
			$accountId = $responseData['data']['userId'];
			// 创建profile
   			$accountid = $responseData['data']['userId'];
   			$nickname = $responseData['data']['nickName'];
   			$gender = $responseData['data']['gender'];
   			$gender = $gender=="MALE"?1:0;
			 
			$profileData = compact('accountid', 'nickname', 'gender');
			$loginResponse = $this->Login->createProfileAndLogin($profileData);
			$loginResponse['binding_platforms'] = $responseData['data']['bindPlatformList'];
			
			$this->_respond(Err::$SUCCESS, $loginResponse);
		}
		else {//认证失败
			Log::write("initnickname:10:$data:$result", 'error');
			$ret = array($responseData['code'], $responseData['message']);
			return $this->_respond($ret);
		}
	}
	
	// 用户忘记密码
	public function www_forget_password() {
		//获取输入参数
		$userId = Auth::user('accountid');
		$email = trim($this->_getParam('email'));
		if( false ) {//local服务提供找回密码
			
		}
		else {  //向用户服务中心提交找回密码申请		
			$data['email'] = $email;
			$data['appId'] = APP_ID;
			$data['url'] = 'http://www.gangker.com/action/resetpwd.php';
			$data['language']  = "Chinese";
			$data = json_encode($data);
			$result = UsersApi::findPassword($data);
			$responseData = json_decode($result, true);
			if (empty($responseData))
				throw new ErrRtnException(Err::$USER_SERVICE_INVALID);
			if ($responseData['code'] == USER_SERVICE_SUCCESS) { //找回密码成功
				$this->_respond(Err::$SUCCESS);
			}
			else {//失败
				$ret = array($responseData['code'], $responseData['message']);
				return $this->_respond($ret);
			}
		}
	}
	
	//修改密码（忘记密码时，通过邮箱的链接修改密码）
	public function www_renew_password() {
		//获取输入参数
		$userId = Auth::user('accountid');
		$newPassword1 = trim($this->_getParam('password1'));
		$newPassword2 = trim($this->_getParam('password2'));
		$token = trim($this->_getParam('token'));
		$email = trim($this->_getParam('email'));
		//校验输入的合法性
		$this->Validator->validatePassword($newPassword1);
		$this->Validator->validatePassword($newPassword2);
		if (strcmp($newPassword1, $newPassword2) != 0) {
			$this->_respond(Err::$FAIL);
		}
		if( false ) {//local服务提供修改密码
	
		}
		else {  //向用户服务中心提交修改密码申请
	
			$data = array('appId' => APP_ID, 'email' => $email, "password"=>$newPassword1, 'token' => $token, 'language' => 'Chinese');
			$data = json_encode($data);
			$result = UsersApi::updateNewPassword($data);
	
			$responseData = json_decode($result, true);
			if ($responseData['code'] == USER_SERVICE_SUCCESS) { //修改密码成功
				$this->_respond(array(0, '密码修改成功'));
			}
			else {//失败
				Log::write("updatepassword:12:$data:$result", 'error');
				$ret = array($responseData['code'], $responseData['message']);
				return $this->_respond($ret);
			}
		}
	}

	public function www_update_password() {
	    //获取输入参数
        $userId = Auth::user('accountid');
        $oldPassword = trim($this->_getParam('old_pwd'));
        $newPassword = trim($this->_getParam('new_pwd'));
        //校验输入的合法性，并判断昵称是否重复
        $this->Validator->validatePassword($oldPassword);
        $this->Validator->validatePassword($newPassword);
        
        if( false ) {//local服务提供修改密码

        }
        else {  //向用户服务中心提交修改密码申请
	    	$data = array('appId' => APP_ID, 'userId' => $userId, "oldPassword"=>$oldPassword, "newPassword"=>$newPassword); 
	    	$data = json_encode($data);
	    	$result = UsersApi::changePassword($data);
	    	
	    	$responseData = json_decode($result, true);
           	if ($responseData['code'] == USER_SERVICE_SUCCESS) { //修改密码成功
           		$this->_respond(Err::$SUCCESS);
	    	}
	    	else {//失败
	    		Log::write("changepassword:12:$data:$result", 'error');
	    		$ret = array($responseData['code'], $responseData['message']);
	    		return $this->_respond($ret);
	    	}
        }
	}
	
	public function www_preference() {
		$accountid = Auth::user('accountid');
		return $this->success(Preference::getAll($accountid));
	}
	
	public function www_update_preference() {
		$accountid = Auth::user('accountid');
		foreach ($this->request->data as $k => $v) {
			if($v == '') continue;
			Preference::set($accountid, $k, $v);
		}
		
		return $this->success();
	}	
	
	public function www_update_profile() {
		$accountid = Auth::user('accountid');
		$profile = UserProfile::findByPk($accountid);
		
		$params = $this->getParams(array('nickname','gender','native_place','occupation','dialect','birthday','introduction','bg_image_id'));		
		
		if($params['nickname']) {
			$this->Validator->validateNickName($params['nickname'], $accountid);
			$profile->nickname = $params['nickname'];
			
//     		if ($profile) {
//     			//通知认证中心修改昵称
//     			$requestData = array('appId' => APP_ID, "userId"=>$accountid, "nickName"=>$profile->nickname );
//     			$requestData = json_encode($requestData);
    			
//     			$result = UsersApi::changeNickname($requestData);
//     	    	$responseData = json_decode($result, true);
//     	    	if (empty($responseData)) {
//     	    		$this->failed(Err::$FAILED);
//     	    	}
//     		}
		}
		
		if(isset($this->request->data['gender'])) {
			$gender = $this->request->data['gender'];
			$this->Validator->validateGender($gender);
			$profile->gender = ''.$gender;
		}
		
		if($params['native_place']) {
			$profile->native_place = $params['native_place'];
		}
		
		if($params['occupation']) {
			$profile->occupation = $params['occupation'];
		}
		
		if($params['dialect']) {
			$profile->dialect = $params['dialect'];
		}
		
		if($params['introduction']) {
			$profile->introduction = $params['introduction'];
		}
		
		if($params['bg_image_id']) {
			$profile->bg_image_id = $params['bg_image_id'];
		}
		
		if($params['birthday']) {
			if(strtotime($params['birthday']) === false) {
				throw new ErrRtnException(Err::$INPUT_FORMAT_INVALID);
			}
			$profile->birthday = $params['birthday'];
		}
		
		$profile->save();
		$this->success();
	}
	
	public function www_update_profile_upload()
	{
		$accountid = Auth::user('accountid');
		
		$type = $this->_getParam('type');
		$profile = UserProfile::findByPk($accountid);
		
		$url = '';
		$file = $this->request->params['form']['upload'];
		if($type == ApiConst::PROFILE_UPLOAD_TYPE_AVATAR) {
			$avatar = $this->Uploader->uploadImage($file);
			$image = Image::load_from_url($avatar);
			$image->resize(Image::SIZE_160x160);
			$image->resize(Image::SIZE_80x80);
			$profile->avatar = $image->get_url(Image::SIZE_160x160);
			$url = $profile->avatar; 
		}
		else if($type == ApiConst::PROFILE_UPLOAD_TYPE_BACKGROUND_IMAGE) {
			$profile->voice = $this->Uploader->uploadImage($file);
			$url = $profile->voice;
		}
		else if($type == ApiConst::PROFILE_UPLOAD_TYPE_VOICE_SIGN) {
			$profile->voice = $this->Uploader->uploadVoice($file, 0);
			$url = $profile->voice;
		}
		
		if($profile->save()) {
			return $this->_respond(Err::$SUCCESS, array('id'=>$accountid, 'file_url'=>$url));
		}
		else {
			return $this->_respond(Err::$FAILED);
		}
	}	
}