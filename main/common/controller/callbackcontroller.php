<?php

class CallbackController extends Controller {
    public $autoRender = false;
    const STATE_LOGIN = 1;
    const STATE_BIND = 2;
    public function www_weibo() {
        OpenApi::createApi('wb');
        if (empty($_REQUEST['code']))
            return;
        $code = $_REQUEST['code'];
        $state = isset($_REQUEST['state']) ? $_REQUEST['state'] : self::STATE_LOGIN;
        try {
            $token = OpenApi::getAccessToken($code);
            //token keys: access_token, expires_in, remind_in, uid
            if ($token) {
                //$data = OpenApi::getOpenId();
                //data keys: uid
                
                $data = OpenApi::getUserInfo($token['uid']);
                //data keys: id, idstr,screen_name, name, province, city, profile_img_url, ....
                if (is_array($data) && !isset($data['error'])) {
                    $userData = array(
                        'platform_name' => 'wb',
                        'access_token' => $token['access_token'],
                        'expires_in' => $token['expires_in'],
                        'refresh_token' => '',
                        'openid' => $token['uid'],
                        'name' => $data['screen_name'],
                        'profile_img' => '',
                    );
                    if ($state == self::STATE_LOGIN)
                        $this->_handleLogin($userData, 'wb');
                    else
                        $this->_handleBind($userData, 'wb');
                    return;
                }
            }
            $errMsg = Err::$FAIL;
            $this->_respond(array('error'=>$errMsg[0], 'errmsg'=>$errMsg[1]));
        } catch (ErrRtnException $e) {
            $this->_respond(array('error' => $e->getCode(), 'errmsg' => $e->getMessage()));
        } catch (Exception $e) {
            $errMsg = Err::$FAIL;
            $this->_respond(array('error' => $errMsg[0], 'errmsg' => $errMsg[1]));
        }
    }
    
    public function www_qq() {
        OpenApi::createApi('qq');
        if (empty($_REQUEST['code']))
            return;
        $code = $_REQUEST['code'];
        $state = isset($_REQUEST['state']) ? $_REQUEST['state'] : self::STATE_LOGIN;
        try {
            $token = OpenApi::getAccessToken($code);
            //token keys: access_token, expires_in
            $dataOpenId = OpenApi::getOpenId();
            //data keys: client_id, openid
            
            $dataUserInfo = OpenApi::getUserInfo($dataOpenId['openid']);
            //data keys: ret, msg, nickname, figureurl, figureurl_1, figureurl_2, gender, vip, level
            $userData = array(
                'platform_name' => 'qq',
                'access_token' => $token['access_token'],
                'expires_in' => $token['expires_in'],
                'refresh_token' => '',
                'openid' => $dataOpenId['openid'],
                'name' => $dataUserInfo['nickname'],
                'profile_img' => '',
            );
            if ($state == self::STATE_LOGIN)
                $this->_handleLogin($userData, 'qq');
            else
                $this->_handleBind($userData, 'qq');
            return;
        } catch (ErrRtnException $e) {
            $this->_respond(array('error' => $e->getCode(), 'errmsg' => $e->getMessage()));
        } catch(Exception $e) {
            $errMsg = Err::$FAIL;
            $this->_respond(array('error' => $errMsg[0], 'errmsg' => $errMsg[1]));
        }

        
    }
    
    public function www_renren() {
        OpenApi::createApi('rr');
        if (empty($_REQUEST['code']))
            return;
        $code = $_REQUEST['code'];
        $state = isset($_REQUEST['state']) ? $_REQUEST['state'] : self::STATE_LOGIN;
        try {
            $token = OpenApi::getAccessToken($code);
            //token keys: expires_in, refresh_token, user(id, name ,avatar(4items), access_token
            $userData = array(
                'platform_name' => 'rr',
                'access_token' => $token['access_token'],
                'expires_in' => $token['expires_in'],
                'refresh_token' => $token['refresh_token'],
                'openid' => $token['user']['id'],
                'name' => $token['user']['name'],
                'profile_img' => '',
            );
            if ($state == self::STATE_LOGIN)
                $this->_handleLogin($userData, 'rr');
            else
                $this->_handleBind($userData, 'rr');
            return;
        } catch (ErrRtnException $e) {
            $this->_respond(array('error' => $e->getCode(), 'errmsg' => $e->getMessage()));
        } catch (Exception $e) {
            $errMsg = Err::$FAIL;
            $this->_respond(array('error' => $errMsg[0], 'errmsg' => $errMsg[1]));
        }
    }
    
    
    //从第三方平台登录认证成功后将进入到此过程，在初始登录时，或在登录后绑定第三方账号都会调用此过程
    protected function _handleLogin($userData, $platform) {
        $accountId = $this->_ensureOpenAccount($userData['openid'], $platform);
        if (!($accountId > 0))
            throw new ErrRtnException(Err::$FAIL);

        $this->_handleBind($userData, $platform, $accountId);
        
        $return = array('error' => 0, 'accountid' => $accountId);

        $profile = UserProfile::instance()->getNameAvatar($accountId);
        if ($profile) {
            $return['needprofile'] = 0;
            $return['nickname'] = $profile['nickname'];
            $return['avatar'] = $profile['avatar'];
        } else {
            $return['needprofile'] = 1;
            $return['openname'] = $userData['name'];
        }
        $this->_respond($return);
        Auth::login(array('accountid' => $accountId, 'platform' => $userData));
    }
    
    protected function _handleBind($userData, $platform, $accountId = null) {
        $needRespond = isset($accountId) ? false : true;
        if (!isset($accountId)) {
            if (!Auth::loggedIn())
                throw new ErrRtnException(Err::$NOTLOGIN);
            $accountId = Auth::user('accountid');
        }
        $result = false;
        $bindId = BindUser::instance()->getBindId($accountId, $platform, $userData['openid']);
        if ($bindId >= 0) {
            $data = array(
                'accesstoken' => $userData['access_token'],
                'expiretime' => date(DATETIMEFORMAT, time() + $userData['expires_in']),
                'refreshtoken' => $userData['refresh_token']
            );
            $result = BindUser::instance()->update($data, 'id = ?', array($bindId), $accountId);
        } else {
            $data = array(
                'accountid' => $accountId,
                'platform' => $userData['platform_name'],
                'openid' => $userData['openid'],
                'accesstoken' => $userData['access_token'],
                'expiretime' => date(DATETIMEFORMAT, time() + $userData['expires_in']),
                'memo' => $userData['refresh_token']
            );
            $result = BindUser::instance()->create($data);
        }
        if ($needRespond) {
            if ($result) {
                $errMsg = Err::$SUCCESS;
            } else {
                $errMsg = Err::$FAIL;
            }
            $this->_respond(array('error' => $errMsg[0], 'errmsg' => $errMsg[1]));
        }
    }
    
    //openid的用户存储在openusers表中，并且在内部自动创建一个新的accountid与之对应
    protected function _ensureOpenAccount($openId, $platform) {
        $bindAccountId = OpenUser::instance()->getAccountId($openId, $platform);
        if ($bindAccountId) {
            return $bindAccountId;
        }
        $data = User::instance()->findUserByUserId($openId . '.' . $platform);
        if ($data) {
            $lastId = $data['accountid'];
            if (strtotime($data['locktime']) > time())
                throw ErrRtnException(Err::$LOGIN_USER_LOCKED);
        } else {
            $data = array(
                'userid' => $openId . '.' . $platform
            );
            
            //第三方平台创建的userid，其密码初始为空
            $lastId = User::instance()->create($data);
            if ($lastId < 0) {
                throw ErrRtnException(Err::$LOGIN_OPENID_BOUND_FAIL);
            }
        }
        
        $data = array(
            'accountid' => $lastId,
            'openid' => $openId,
            'platform' => $platform
        );
        if (OpenUser::instance()->create($data)) {
            return $lastId;
        } else {
            throw ErrRtnException(Err::$LOGIN_OPENID_BOUND_FAIL);
        }
    }
    
    
    protected function _respond($data) {
        echo '<html><title>login</title><body><div id="return" style="display:block">';
        foreach($data as $key => $value) {
            echo '<'.$key.'>'.$value.'</'.$key.'>';
        }
        echo '</div></body></html>';
    }
    

    
}