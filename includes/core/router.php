<?php
/**
 * Parses the request URL into controller, action, and parameters.
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



class Router {


    public static function parse($url) {
        $url = trim($url, '/');
        $fields = explode('/', strtolower($url), 4);
        $out = array();
        if (count($fields) > 0 && in_array($fields[0], config('prefix'))) {
            $out['prefix'] = $fields[0];
            array_shift($fields);
        } else {
            $out['prefix'] = '';
        }

        if (count($fields) === 0) {
            $out['controller'] = $out['action'] = '';
        } else if (count($fields) === 1) {
            $out['controller'] = $fields[0];
            $out['action'] = 'index';
        } else {
            $out['controller'] = $fields[0];
            $out['action'] = $fields[1];
        }

        return $out;
    }

    public static function url($url = null, $request = null) {
        if (!isset($request)) {
            global $hdRequest;
            $request = $hdRequest;
        }
        
        $fullurl = $request->webroot;
        if (is_array($url)) {
            if (isset($url['prefix']))
                $fullurl .= $url['prefix'] . '/';
            if (isset($url['controller']))
                $fullurl .= $url['controller'] . '/';
            if (isset($url['action']))
                $fullurl .= $url['action'];
        } else {
            $fullurl .= $url;
        }
        return $fullurl;
    }
    
}

