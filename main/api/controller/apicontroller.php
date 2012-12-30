<?php

/**
 * 控制器基类
 * @property UploaderComponent $Uploader
 * @property ValidatorComponent $Validator
 * @author Administrator
 */
class ApiController extends Controller {
    public $autoRender = false;
    public $autoLayout = false;
    public $components = array('Uploader','Validator');
    
    public function __construct(Request $request = null, Response $response = null) {
        parent::__construct($request, $response);
    }
    
    public function beforeFilter() {    	
        /*$clientIp = $this->request->clientIp();
    	if (!UserProfile::instance()->isIpPermitted($clientIp)) //ip不允许
    		return false;*/
    	// xhprof
    	if(config('debug') == 2 && false) {
    		xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
    	}
    	
        if ($this->request->is('mobile'))
            setConfig('debug', 0);
        
        if (!Auth::loggedIn() && !Auth::allowed($this->request['action'])) {
            $this->_respond(Err::$LOGIN_NEEDED);
            return false;
        }
        $debugMode = $this->_getParam('debug', false);

        if ($debugMode && config('debug') > 1) {
            $this->autoRender = true;
            $this->view = 'api';
        }
        
        //如果不是longin，register等方法，刷新统计数据和用户活动时间
        if (!Auth::allowed($this->request['action']))	{ 
        	$accountid = Auth::user('accountid');
        	OnlineUser::touch($accountid);
        }
        return true;
    }
    
    public function afterFilter() {
        $debugMode = $this->_getParam('debug', false);
        if ($debugMode && config('debug') > 1) {
            //$this->

        }
        //如果是longin，register方法，刷新统计数据和用户活动时间
        $allowedAction = array('login', 'index', 'logout', 'showapi', 'register', 'version', 'tlogin', 'weibo', 'qq', 'renren', 'findpassword','initnickname');
        if (Auth::loggedIn() && in_array($this->request['action'] ,$allowedAction))	{ 
        	//$this->_refreshStatistics();
        }
        
        //xhprof disable
        if(config('debug') == 2 && false) {
	        $xhprof_data = xhprof_disable();
	        $XHPROF_ROOT = MAIN_ . 'lib';
			include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
			include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
			$xhprof_runs = new XHProfRuns_Default();
			$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_venus"); //source名称是xhprof_foo
        }				        
    }
    
   //刷新当前用户最近活动时间，更改api访问次数数据
   private function _refreshStatistics() {
        $controller = $this->request['controller'];
        $action = $this->request['action'];
        $accountId = Auth::user('accountid');
   }
        
    public function invokeAction(Request $request) {
        try {
            return parent::invokeAction($request);
        } catch(ErrRtnException $e) {
            $this->_respond(array($e->getCode(), $e->getMessage()));
        } catch(Exception $e) {
            Log::write('['.$request->params['controller'].'/'.$request->params['action'].
                ']('.Auth::user('accountid').'):'. $e->getMessage(). ',{'.
                $e->getFile() . '(' . $e->getLine() .')}', 'error');
            if (config('debug')) {
                $sql = $e instanceof PDOException ? $e->queryString : '';
                $this->_respond(array($e->getCode(), $e->getMessage()), $sql);
            } else {
                $this->_respond(Err::$FAIL);
            }
        }
        
    }

    /**
     * 成功应答
     * @param array $data 返回数据
     * @param string $popup
     * @param bool $exit 是否结束脚本执行
     */
    protected function success($data=null, $popup=null, $exit=true) {
    	$this->_respond(Err::$SUCCESS, $data, $popup, $exit);
    }
    
    /**
     * 失败应答
     * @param array $error 错误
     * @param array $data 详细错误信息
     * @param string $popup
     * @param bool $exit 是否结束脚本执行
     */
    protected function failed($error, $data = null, $popup=null, $exit=true) {
		$this->_respond($error, $data, $popup, $exit);
    }
    
    protected function _respond($errMsg, $data = null, $popup = null, $exit=false) {
        if (!isset($data))
           $data = '';
        if (!isset($popup))
           $popup = '';
                      
        Utility::patchArray($data);
        $result = json_encode(array(
           'code' => $errMsg[0],
           'message' => $errMsg[1],
           'data' => $data,
           'popup' => $popup
        ));
        if (!$this->autoRender) {
            echo $result;
            if($exit) exit;
        } else {
            $this->set('apiResult', $result);
        }
    }
    
    protected function _getParam($name, $default = null, $trim=true) {
    	$value = $default;
        if (isset($this->request->data[$name]))
            $value = $this->request->data[$name];
        else if (isset($this->request->query[$name]))
            $value = $this->request->query[$name];
        
        $value = $trim?trim($value):$value;
        if (empty($value)) {
        	return $default;
        }
       	return $value;
    }
    
    protected function getParams($params) {
    	$data = array();
    	foreach ($params as $value) {
    		$data[$value] = $this->_getParam($value);
    	}
    	return $data;
    }
    
    protected function pageParams() {
    	$page = $this->_getParam('page', 1);
    	$count = $this->_getParam('count', 20);
    	$sinceid = $this->_getParam('sinceid', false);
    	$maxid = $this->_getParam('maxid', false);
    	
    	if(empty($page)) $page = 1;
    	if(empty($count)) $count = 20;
    	if(empty($sinceid)) $sinceid = false;
    	if(empty($maxid)) $maxid = false;
    	
    	return compact('page','count','sinceid','maxid');
    }
    
    protected function _getSaltedPassword($password) {
        return md5($password . config('securitysalt'));
    }
}