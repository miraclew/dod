<?php

class Auth {

    public static $sessionKey = 'Auth.User';
    public static $allowedActions = array('login', 'index', 'logout', 'register', 'auto_login', 't_login', 'weibo', 'qq', 'renren', 'forget_password', 'renew_password', 't_init_nickname');

    protected static $_user;


    public static function login($user = null) {
        if ($user) {
            Session::instance()->renew();
            Session::instance()->write(self::$sessionKey, $user);
        }
        return self::loggedIn();
    }

    public static function logout() {
        //Session::instance()->delete(self::$sessionKey);
        Session::instance()->destroy();
        
        //Session::instance()->renew();
    }


    public static function user($key = null) {
        if (empty(self::$_user) && !Session::instance()->check(self::$sessionKey)) {
            return null;
        }
        if (!empty(self::$_user)) {
            $user = self::$_user;
        } else {
            $user = Session::instance()->read(self::$sessionKey);
        }
        if ($key === null) {
            return $user;
        }
        if (isset($user[$key])) {
            return $user[$key];
        }
        return null;
    }

        	public $components = array(
    'RequestHandler'
);

    public static function loggedIn() {
        if (self::user()) {
        	return true;
        }
        else
            return false;
    }
    
    public static function allowed($action) {
        return in_array($action, self::$allowedActions);
    }
    
    public static function isSelfUser() {
        $platform = self::user('platform');
        return empty($platform) ? true : false;
    }
    
    public static function isWeiboUser() {
        $platform = self::user('platform');
        if ($platform && isset($platform['platform_name'])) {
            if ($platform['platform_name'] == 'wb')
                return true;
        }
        return false;
    }
    
    public static function getUserPlatformName() {
        $platform = self::user('platform');
        if ($platform && isset($platform['platform_name'])) {
            return $platform['platform_name'];
        }
        return 'self';
    }
    
    public static function isWeibo() {
        $platform = self::user('platform');
        if ($platform && isset($platform['platform_name'])) {
            if ($platform['platform_name'] == 'wb')
                return true;
        }
        return false;
    }
     

    /*
    public function allow($action = null) {
        $args = func_get_args();
        if (empty($args) || $args == array('*')) {
            $this->allowedActions = $this->_methods;
        } else {
            if (isset($args[0]) && is_array($args[0])) {
                $args = $args[0];
            }
            $this->allowedActions = array_merge($this->allowedActions, $args);
        }
    }


    public function deny($action = null) {
        $args = func_get_args();
        if (empty($args)) {
            $this->allowedActions = array();
        } else {
            if (isset($args[0]) && is_array($args[0])) {
                $args = $args[0];
            }
            foreach ($args as $arg) {
                $i = array_search($arg, $this->allowedActions);
                if (is_int($i)) {
                    unset($this->allowedActions[$i]);
                }
            }
            $this->allowedActions = array_values($this->allowedActions);
        }
    }
    */
    
}
