<?php
/**
 * Dispatcher takes the URL information, parses it for parameters and
 * tells the involved controllers what to do.
 *
 * This is the heart of Cake's operation.
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
 * @package       Cake.Routing
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


class Dispatcher {

    public function dispatch(Request $request, Response $response, $additionalParams = array()) {
        if ($this->asset($request->url, $response)) {
            return;
        }

        $request = $this->parseParams($request);
        $controller = $this->_getController($request, $response);

        if (!($controller instanceof Controller)) {
            throw new MissingControllerException(array(
                'class' => ucfirst($request->params['controller'])
                          .ucfirst($request->params['prefix']) . 'Controller',
            ));
        }
        return $this->_invoke($controller, $request, $response);
    }


    protected function _invoke(Controller $controller, Request $request, Response $response) {
        $render = true;
        $result = false;
        if ($controller->beforeFilter()) {
            $result = $controller->invokeAction($request);
        }
        $controller->afterFilter();
        if ($result instanceof Response) {
            $render = false;
            $response = $result;
        }

        if ($render && $controller->autoRender) {
            $response = $controller->render();
        } elseif ($response->body() === null) {
            $response->body($result);
        }

        if (isset($request->params['return'])) {
            return $response->body();
        }
        $response->send();
    }


    protected function _getController($request, $response) {
        $ctrlClass = $this->_loadController($request);
        if (!$ctrlClass) {
            return false;
        }
        $reflection = new ReflectionClass($ctrlClass);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            return false;
        }
        return $reflection->newInstance($request, $response);
    }

/**
 * Load controller and return controller classname
 *
 * @param CakeRequest $request
 * @return string|bool Name of controller class name
 */
    protected function _loadController($request) {
        $controller = null;
        if (!empty($request->params['controller'])) {
            $controller = $request->params['controller'];
        }
        $prefix = null;
        if (!empty($request->params['prefix'])) {
            $prefix = $request->params['prefix'];
        }
        
        if ($controller) {
            $class = ucfirst($controller) . ucfirst($prefix) . 'Controller';            
            AutoLoader::load($class);
            if (class_exists($class)) {
                return $class;
            }
        }
        return false;
    }

/**
 * Checks if a requested asset exists and sends it to the browser
 *
 * @param string $url Requested URL
 * @param Response $response The response object to put the file contents in.
 * @return boolean True on success if the asset file was found and sent
 */
    public function asset($url, Response $response) {
        if (strpos($url, '..') !== false || strpos($url, '.') === false) {
            return false;
        }
        
        $pathSegments = explode('.', $url);
        $ext = array_pop($pathSegments);
        $parts = explode('/', $url);
        
        $assetFile = null;
        $module = config('modules.'.$parts[0], null);
        if ($module) {
            if (!empty($module['public_'])) {
                unset($parts[0]);
                $fileFragment = implode(DS, $parts);
                $assetFile = $module['public_'] . $fileFragment;
            }
        }
        if ($assetFile && file_exists($assetFile)) {
            $this->_deliverAsset($response, $assetFile, $ext);
            return true;
        }
        return false;

    }

/**
 * Sends an asset file to the client
 *
 * @param CakeResponse $response The response object to use.
 * @param string $assetFile Path to the asset file in the file system
 * @param string $ext The extension of the file to determine its mime type
 * @return void
 */
    protected function _deliverAsset(Response $response, $assetFile, $ext) {
        ob_start();
        $compressionEnabled = config('asset.compress') && $response->compress();
        if ($response->type($ext) == $ext) {
            $contentType = 'application/octet-stream';
            $agent = env('HTTP_USER_AGENT');
            if (preg_match('%Opera(/| )([0-9].[0-9]{1,2})%', $agent) || preg_match('/MSIE ([0-9].[0-9]{1,2})/', $agent)) {
                $contentType = 'application/octetstream';
            }
            $response->type($contentType);
        }
        if (!$compressionEnabled) {
            $response->header('Content-Length', filesize($assetFile));
        }
        $response->cache(filemtime($assetFile));
        $response->send();
        ob_clean();
        if ($ext === 'css' || $ext === 'js') {
            include($assetFile);
        } else {
            readfile($assetFile);
        }

        if ($compressionEnabled) {
            ob_end_flush();
        }
    }



    public function parseParams($request) {
        //debug($request->url) will output 'tests' if url in brower is 'localhost/venus/tests' ;
        $params = Router::parse($request->url);
        $request->addParams($params);

        return $request;
    }

}