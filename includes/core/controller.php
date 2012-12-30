<?php
/**
 * Base controller class.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class Controller extends Object {

    public $name = null;
    public $uses = false;
    public $helpers = false;
    public $components = false;

    public $request;
    public $response;

    public $viewClass = 'View';
    public $View;
    
    public $view = null;
    public $viewPath = null;
    public $layout = 'default';
    public $layoutPath = null;
    public $ext = '.ctp';
        
    public $autoRender = true;
    public $autoLayout = true;

    public $viewVars = array();
    
    public function __construct(Request $request = null, Response $response = null) {
        $this->request = $request;
        $this->response = $response;
        if (!isset($this->view) && isset($request->params['action']))
            $this->view = $request->params['action'];
        if (!isset($this->viewPath) && isset($request->Params['action']))
            $this->viewPath = $request->params['controller'];
        if (array_key_exists('return', $request->params) && $request->params['return'] == 1) {
            $this->autoRender = false;
        }
        
        $this->_initComponent();
        
        parent::__construct();
    }
    

    public function beforeFilter() {
        return true;
    }
    
    public function afterFilter() {
        return false;
    }
    
    protected function _initHelper() {
        if (!$this->helpers) {
            return;
        }
        foreach ($this->helpers as $helper) {
            $this->{$helper} = new $helper($this);
        }
    }
    
    protected function _initComponent() {
    	if(!$this->components) {
    		return;
    	}
    	foreach ($this->components as $component) {
    		$class = $component.'Component';    		
    		$this->{$component} = new $class($this);
    		$this->{$component}->initialize();
    	}
    }
    
    public function invokeAction(Request $request) {
        try {
            $method = new ReflectionMethod($this, 'www_' . $request->params['action']);
            return $method->invokeArgs($this, array());

        } catch (ReflectionException $e) {
            throw new MissingActionException(array(
                'controller' => get_class($this),
                'action' => 'www_'.$request->params['action']
            ));
        }
    }


    public function httpCodes($code = null) {
        return $this->response->httpCodes($code);
    }


    public function redirect($url, $status = null, $exit = true) {
        $this->autoRender = false;

        if (function_exists('session_write_close')) {
            session_write_close();
        }

        if (!empty($status) && is_string($status)) {
            $codes = array_flip($this->response->httpCodes());
            if (isset($codes[$status])) {
                $status = $codes[$status];
            }
        }

        if ($url !== null) {
            $this->response->header('Location', Router::url($url, true));
        }

        if (!empty($status) && ($status >= 300 && $status < 400)) {
            $this->response->statusCode($status);
        }

        if ($exit) {
            $this->response->send();
            $this->_stop();
        }
    }


    public function header($status) {
        $this->response->header($status);
    }


    public function set($one, $two = null) {
        if (!isset($one))
            return;
            
        if (is_array($one)) {
            if (is_array($two)) {
                $data = array_combine($one, $two);
            } else {
                $data = $one;
            }
        } else {
            $data = array($one => $two);
        }
        $this->viewVars = $data + $this->viewVars;
    }


    public function setAction($action) {
        $this->request->action = $action;
        $this->view = $action;
        $args = func_get_args();
        unset($args[0]);
        return call_user_func_array(array(&$this, $action), $args);
    }


    public function render($view = null, $layout = null) {    	
        if (empty($this->viewClass))
            $viewClass = 'View';
        else
            $viewClass = $this->viewClass;

        $View = new $viewClass($this);

        $this->autoRender = false;
        $this->View = $View;
        $this->response->body($View->render($view, $layout));
        return $this->response;
    }


    public function referer($default = null, $local = false) {
        if ($this->request) {
            $referer = $this->request->referer($local);
            if ($referer == '/' && $default != null) {
                return Router::url($default, true);
            }
            return $referer;
        }
        return '/';
    }


    public function disableCache() {
        $this->response->disableCache();
    }

    public function flash($message, $url, $pause = 1, $layout = 'flash') {
        $this->autoRender = false;
        $this->set('url', Router::url($url));
        $this->set('message', $message);
        $this->set('pause', $pause);
        $this->set('page_title', $message);
        $this->render(false, $layout);
    }


}
