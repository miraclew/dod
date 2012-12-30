<?php


class HttpRequest {
    
    public function __construct() {
        return;
    }
    //发送post请求
    
    static public function post($req, $data, $header=array(), $timeout=7) {
        $url = self::makeUri($req);
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }
        if (!is_array($data)) {
            throw new Exception('data param not a array');
        }
        $header[] = 'Expect:';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $data = curl_exec($ch);
        if ($data === false) //timeout
        	return false;
        list($header, $data) = explode("\r\n\r\n", $data);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 301 || $http_code == 302) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = trim(array_pop($matches));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
        }

        if ($data == false) {
            curl_close($ch);
        }
        @curl_close($ch);
        return $data;
    }
    //发送get请求
    
    static public function get($req, $header=array(), $timeout=5) {
        $url = self::makeUri($req);
        if (!function_exists('curl_init')) {
            throw new Exception('server not install curl');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        $data = curl_exec($ch);
        list($header, $data) = explode("\r\n\r\n", $data);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 301 || $http_code == 302) {
            $matches = array();
            preg_match('/Location:(.*?)\n/', $header, $matches);
            $url = trim(array_pop($matches));
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
        }

        if ($data == false) {
            curl_close($ch);
        }
        @curl_close($ch);
        return $data;
    }

    static public function head($req, $timeout=5) {
        $url = self::makeUri($req);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER,         true);
        curl_setopt($ch, CURLOPT_NOBODY,         true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,        $timeout);
        $headers = curl_exec($ch);
        if ($headers === false) {
            return false;
        }
        $headers = str_replace("\r",'',$headers);
        $headers = explode("\n",$headers);
        foreach($headers as $value) {
            $header = explode(': ',$value);
            if ($header[0] && !$header[1]) {
                $headerdata['status'] = $header[0];
            }
            elseif ($header[0] && $header[1]) {
                $headerdata[$header[0]] = $header[1];
            }
        }
        return $headerdata;
    }

    
    public static function sendRequest($req, $host='') {
        $url = self::makeUri($req);
        $urlArr = parse_url($url);
        $fp = @fsockopen($urlArr['host'], 80, $errno, $errstr, 1);
        if ($fp) {
            stream_set_timeout($fp,1);
            $out = "GET {$urlArr['path']}?{$urlArr['query']} HTTP/1.1\r\n";
            if (!empty($host)) {
                $out .= "Host: {$host}\r\n";
            } else {
                $out .= "Host: {$urlArr['host']}\r\n";
            }
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fp, $out);
            fclose ($fp);
        }
    }

    private static function makeUri($req) {
        if (is_array($req) && (count($req)==2)) {
            list($url, $params) = $req;
            foreach($params as $k=>$v) {
                $params[$k] = "{$k}={$v}";
            }
            if (strpos($url, '?') !== false) {
                $url .= '&'.implode('&', $params);
            } else {
                $url .= '?'.implode('&', $params);
            }
        } else {
            $url = $req;
        }
        return $url;
    }
}