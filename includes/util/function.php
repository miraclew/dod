<?php

/**
 * Basic defines for timing functions.
 */
    define('SECOND', 1);
    define('MINUTE', 60);
    define('HOUR', 3600);
    define('DAY', 86400);
    define('WEEK', 604800);
    define('MONTH', 2592000);
    define('YEAR', 31536000);

    
    

function config($name, $default = null) {
    global $hdConfig;
    if (isset($hdConfig[$name])) {
       return $hdConfig[$name];
    } else if (strpos($name, '.') === false) {
        return $default;
    } else {
        $names = explode('.', $name, 3);
        $name = $names[0];
        if (!isset($hdConfig[$name]))
            return $default;
        if (count($names) == 2) {
            if (!isset($hdConfig[$name][$names[1]]))
                return $default;
            return $hdConfig[$name][$names[1]];
        } else if (count($names) == 3) {
            if (!isset($hdConfig[$name][$names[1][$name[2]]]))
                return $default;
            return $hdConfig[$name][$names[1][$name[2]]];
        }
    }
}

function dlconfig($name, $default = null) {
    global $dlConfig;
    if (isset($dlConfig[$name])) {
       return $dlConfig[$name];
    } else if (strpos($name, '.') === false) {
        return $default;
    } else {
        $names = explode('.', $name, 3);
        $name = $names[0];
        if (!isset($dlConfig[$name]))
            return $default;
        if (count($names) == 2) {
            if (!isset($dlConfig[$name][$names[1]]))
                return $default;
            return $dlConfig[$name][$names[1]];
        } else if (count($names) == 3) {
            if (!isset($dlConfig[$name][$names[1][$name[2]]]))
                return $default;
            return $dlConfig[$name][$names[1][$name[2]]];
        }
    }
}

function setConfig($name, $value) {
    global $hdConfig;
    if (strpos($name, '.') === false) {
        $hdConfig[$name] = $value;
    } else {
        $names = explode('.', $name, 3);
        if (count($names) == 2) {
            $hdConfig[$names[0]][$names[1]] = $value;
        } else if (count($names) == 3) {
            $hdConfig[$names[0]][$names[1]][$names[2]] = $value;
        }
    }
}

function debugd($var = false, $showHtml = null, $showFrom = true) {
    debug($var, $showHtml, $showFrom);
    die();
}
/**
 * Prints out debug information about given variable.
 *
 * Only runs if debug level is greater than zero.
 *
 * @param boolean $var Variable to show debug information for.
 * @param boolean $showHtml If set to true, the method prints the debug data in a browser-friendly way.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @link http://book.cakephp.org/2.0/en/development/debugging.html#basic-debugging
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#debug
 */
function debug($var = false, $showHtml = null, $showFrom = true) {
    if (config('debug') > 0) {
        $file = '';
        $line = '';
        if ($showFrom) {
            $calledFrom = debug_backtrace();
            $file = substr(str_replace(ROOT_, '', $calledFrom[0]['file']), 1);
            $line = $calledFrom[0]['line'];
        }
        $html = <<<HTML
<div class="cake-debug-output">
<span><strong>%s</strong> (line <strong>%s</strong>)</span>
<pre class="cake-debug">
%s
</pre>
</div>
HTML;
        $text = <<<TEXT

%s (line %s)
########## DEBUG ##########
%s
###########################

TEXT;
        $template = $html;
        if (php_sapi_name() == 'cli') {
            $template = $text;
        }
        if ($showHtml === null && $template !== $text) {
            $showHtml = true;
        }
        $var = print_r($var, true);
        if ($showHtml) {
            $var = h($var);
        }
        printf($template, $file, $line, $var);
    }
}

/**
 * Convenience method for htmlspecialchars.
 *
 * @param mixed $text Text to wrap through htmlspecialchars.  Also works with arrays, and objects.
 *    Arrays will be mapped and have all their elements escaped.  Objects will be string cast if they
 *    implement a `__toString` method.  Otherwise the class name will be used.
 * @param boolean $double Encode existing html entities
 * @param string $charset Character set to use when escaping.  Defaults to config value in 'App.encoding' or 'UTF-8'
 * @return string Wrapped text
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#h
 */
function h($text, $double = true, $charset = null) {
    if (is_array($text)) {
        $texts = array();
        foreach ($text as $k => $t) {
            $texts[$k] = h($t, $double, $charset);
        }
        return $texts;
    } elseif (is_object($text)) {
        if (method_exists($text, '__toString')) {
            $text = (string) $text;
        } else {
            $text = '(object)' . get_class($text);
        }
    }

    static $defaultCharset = false;
    if ($defaultCharset === false) {
        $defaultCharset = config('encoding');
        if ($defaultCharset === null) {
            $defaultCharset = 'UTF-8';
        }
    }
    if (is_string($double)) {
        $charset = $double;
    }
    return htmlspecialchars($text, ENT_QUOTES, ($charset) ? $charset : $defaultCharset, $double);
}


if (!function_exists('sortByKey')) {

    /**
     * Sorts given $array by key $sortby.
     *
     * @param array $array Array to sort
     * @param string $sortby Sort by this key
     * @param string $order  Sort order asc/desc (ascending or descending).
     * @param integer $type Type of sorting to perform
     * @return mixed Sorted array
     */
    function sortByKey(&$array, $sortby, $order = 'asc', $type = SORT_NUMERIC) {
        if (!is_array($array)) {
            return null;
        }

        foreach ($array as $key => $val) {
            $sa[$key] = $val[$sortby];
        }

        if ($order == 'asc') {
            asort($sa, $type);
        } else {
            arsort($sa, $type);
        }

        foreach ($sa as $key => $val) {
            $out[] = $array[$key];
        }
        return $out;
    }
}

/**
 * Searches include path for files.
 *
 * @param string $file File to look for
 * @return Full path to file if exists, otherwise false
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#fileExistsInPath
 */
function fileExistsInPath($file) {
    $paths = explode(PATH_SEPARATOR, ini_get('include_path'));
    foreach ($paths as $path) {
        $fullPath = $path . DS . $file;

        if (file_exists($fullPath)) {
            return $fullPath;
        } elseif (file_exists($file)) {
            return $file;
        }
    }
    return false;
}


/**
 * Convert forward slashes to underscores and removes first and last underscores in a string
 *
 * @param string String to convert
 * @return string with underscore remove from start and end of string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#convertSlash
 */
function convertSlash($string) {
    $string = trim($string, '/');
    $string = preg_replace('/\/\//', '/', $string);
    $string = str_replace('/', '_', $string);
    return $string;
}


/**
 * Returns a translated string if one is found; Otherwise, the submitted message.
 *
 * @param string $singular Text to translate
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return mixed translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__
 */
function __($message, $args = null) {
    global $hdLocale;
    if (isset($hdLocale[$message]))
        $message = $hdLocale[$message];
        
    if ($args === null) {
        return $message;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args(), 1);
    }
    return vsprintf($message, $args);
}

function __e($message, $args = null) {
    global $hdErrMsg;
    if (!isset($hdErrMsg[$message])) {
        if (isset($hdErrMsg['fail']))
            return $hdErrMsg['fail'];
        else
            return array(-1, '');
    }
    $errMsg = $hdErrMsg[$message];
    if ($args === null) {
        return $errMsg;
    } elseif (!is_array($args)) {
        $args = array_slice(func_get_args(), 1);
    }
    return array($errMsg[0], vsprintf($errMsg[1], $args));
}


/**
 * Gets an environment variable from available sources, and provides emulation
 * for unsupported or inconsistent environment variables (i.e. DOCUMENT_ROOT on
 * IIS, or SCRIPT_NAME in CGI mode).  Also exposes some additional custom
 * environment information.
 *
 * @param  string $key Environment variable name.
 * @return string Environment variable setting.
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#env
 */
function env($key) {
    if ($key === 'HTTPS') {
        if (isset($_SERVER['HTTPS'])) {
            return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        }
        return (strpos(env('SCRIPT_URI'), 'https://') === 0);
    }

    if ($key === 'SCRIPT_NAME') {
        if (env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
            $key = 'SCRIPT_URL';
        }
    }

    $val = null;
    if (isset($_SERVER[$key])) {
        $val = $_SERVER[$key];
    } elseif (isset($_ENV[$key])) {
        $val = $_ENV[$key];
    } elseif (getenv($key) !== false) {
        $val = getenv($key);
    }

    if ($key === 'REMOTE_ADDR' && $val === env('SERVER_ADDR')) {
        $addr = env('HTTP_PC_REMOTE_ADDR');
        if ($addr !== null) {
            $val = $addr;
        }
    }

    if ($val !== null) {
        return $val;
    }

    switch ($key) {
        case 'DOCUMENT_ROOT':
            $name = env('SCRIPT_NAME');
            $filename = env('SCRIPT_FILENAME');
            $offset = 0;
            if (!strpos($name, '.php')) {
                $offset = 4;
            }
            return substr($filename, 0, strlen($filename) - (strlen($name) + $offset));
            break;
        case 'PHP_SELF':
            return str_replace(env('DOCUMENT_ROOT'), '', env('SCRIPT_FILENAME'));
            break;
        case 'CGI_MODE':
            return (PHP_SAPI === 'cgi');
            break;
        case 'HTTP_BASE':
            $host = env('HTTP_HOST');
            $parts = explode('.', $host);
            $count = count($parts);

            if ($count === 1) {
                return '.' . $host;
            } elseif ($count === 2) {
                return '.' . $host;
            } elseif ($count === 3) {
                $gTLD = array(
                    'aero',
                    'asia',
                    'biz',
                    'cat',
                    'com',
                    'coop',
                    'edu',
                    'gov',
                    'info',
                    'int',
                    'jobs',
                    'mil',
                    'mobi',
                    'museum',
                    'name',
                    'net',
                    'org',
                    'pro',
                    'tel',
                    'travel',
                    'xxx'
                );
                if (in_array($parts[1], $gTLD)) {
                    return '.' . $host;
                }
            }
            array_shift($parts);
            return '.' . implode('.', $parts);
            break;
    }
    return null;
}

/**
 * Print_r convenience function, which prints out <PRE> tags around
 * the output of given array. Similar to debug().
 *
 * @see debug()
 * @param array $var Variable to print out
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#pr
 */
function pr($var) {
    if (config('debug') > 0) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

/**
 * Merge a group of arrays
 *
 * @param array First array
 * @param array Second array
 * @param array Third array
 * @param array Etc...
 * @return array All array parameters merged into one
 * @link http://book.cakephp.org/2.0/en/development/debugging.html#am
 */
function am() {
    $r = array();
    $args = func_get_args();
    foreach ($args as $a) {
        if (!is_array($a)) {
            $a = array($a);
        }
        $r = array_merge($r, $a);
    }
    return $r;
}



function timediff($from, $to) {
    //$t=strtotime('$timeform')-strtotime('$timeto');
    $y=date("Y",$t)-1970;
    $m=date("m",$t)-1;
    $d=date("d",$t)-1;
    $h=date("H",$t)-1;
    $m=date("M",$t)-1;
    $s=date("S",$t)-1;
    $a=compact("y","m","d","h","m","s");
    return $a;
}

/**
 * Recursively strips slashes from all values in an array
 *
 * @param array $values Array of values to strip slashes
 * @return mixed What is returned from calling stripslashes
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#stripslashes_deep
 */
function stripslashes_deep($values) {
    if (is_array($values)) {
        foreach ($values as $key => $value) {
            $values[$key] = stripslashes_deep($value);
        }
    } else {
        $values = stripslashes($values);
    }
    return $values;
}


function trace() {
    try {
        throw new Exception('trace');
    } catch(Exception $e) {
        echo $e->getTraceAsString();
    }
}

//把\u5efa\u8bae\u60a8\u4fee\u6539\u4f60\u7684\u6635\u79f0格式的字符串转为utf8字符串
function ucs2utf8($str) {
	return preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $str);
}

//检查ip是否在指定范围内
 function ipCIDRCheck ($IP, $CIDR) {
    list ($net, $mask) = explode ("/", $CIDR);
    $ip_net = ip2long ($net);
    $ip_mask = ~((1 << (32 - $mask)) - 1);

    $ip_ip = ip2long ($IP);

    $ip_ip_net = $ip_ip & $ip_mask;

    return ($ip_ip_net == $ip_net);
  }

 //将一个文本文件读为一个数组，每行一个元素 
 function readFile2Array($fileName) {
        $data = file_get_contents($fileName);
        $data = str_replace("\r", "", $data);
        $data = explode("\n", $data);
        return $data;
  }

/**
 * 获取关键字配置文件，并整理格式
 * $type为0为判断模式,为1时为过滤模式
 */
function getFilterWordsStar($type = 0, $fileName) {
	//敏感词 可以考虑放缓存
	$words = file_get_contents (  CONFIG_ . $fileName );
	$words = str_replace ( '"', '', $words );

	$wordsAry = explode ( "\n", $words );
	$newArr = array();
	foreach ( $wordsAry as $k => &$v ) {
		$v = str_replace ( "\n", '', $v );
		$v = str_replace ( "\r", '', $v );
		if (empty ( $v )){
			unset ( $wordsAry [$k] );
		}else{
			if($type == 1){
				$starnum = mb_strlen($v,'utf8');
				$newArr[$v] = str_pad("",$starnum,"*");
			}else{
				$newArr[] = $v;
			}
		}
	}
	return $newArr;
}


/*
 * 返回false说明是正常的，没有关键字
 * 返回true说明包含关键字，或者格式不是字符串
 * $w = 要检测的串
 */
function checkFilterWords($w, $fileName = 'filtertext.txt') {
	if (empty ( $w )) {
		return false;
	}
	if (! is_string ( $w )) {
		return false;
	}
	$filterWordAry =  getFilterWordsStar(0, $fileName);
	$isok = false;
	foreach ( $filterWordAry as $v ) {
		if (false !== stripos ( strtolower ( $w ), strtolower ( $v ) )) {
			$isok = true;
			break;
		}
	}
	return $isok;
}

/**
 * 过滤敏感词,将敏感词替换成*号
 * $input_str = 要过滤的串 返回 替换好的内容
 */ 
function purifyWords($input_str = ""){
	$words  = getFilterWordsStar(1);
	return strtr($input_str,$words);
}

//异或加密解密
function xor_encrypt($str, $key){
	$crytxt = '';
	$keylen = strlen($key);
	for($i=0;$i < strlen($str);$i++){ 
	   $k = $i % $keylen; 
	   $crytxt .= $str[$i] ^ $key[$k];
	}
	return $crytxt; 
} 

//sort an associative array by one of its keys
function sortByOneKey(array $array, $key, $asc = true) {
    $result = array();
        
    $values = array();
    foreach ($array as $id => $value) {
        $values[$id] = isset($value[$key]) ? $value[$key] : '';
    }
        
    if ($asc) {
        asort($values);
    }
    else {
        arsort($values);
    }
        
    foreach ($values as $key => $value) {
        $result[$key] = $array[$key];
    }
        
    return $result;
}

function simplexml2array($xml) {
	if (get_class($xml) == 'SimpleXMLElement') {
		$attributes = $xml->attributes();
		foreach($attributes as $k=>$v) {
			if ($v) $a[$k] = (string) $v;
		}
		$x = $xml;
		$xml = get_object_vars($xml);
	}
	if (is_array($xml)) {
		if (count($xml) == 0) return (string) $x; // for CDATA
		foreach($xml as $key=>$value) {
			$r[$key] = $this->simplexml2array($value);
		}
		if (isset($a)) $r['@attributes'] = $a;    // Attributes
		return $r;
	}
	return (string) $xml;
}

  

