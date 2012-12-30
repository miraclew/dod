<?php
/**
 * CakeRequest
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
 * @since         CakePHP(tm) v 2.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


/**
 * A class that helps wrap Request information and particulars about a single request.
 * Provides methods commonly used to introspect on the request headers and request body.
 *
 * Has both an Array and Object interface. You can access framework parameters using indexes:
 *
 * `$request['controller']` or `$request->controller`.
 *
 * @package       Cake.Network
 */
class Request implements ArrayAccess {
/**
 * Array of parameters parsed from the url.
 *
 * @var array
 */
    public $params = array(
        'controller' => null,
        'action' => null,
    );

/**
 * Array of POST data.  Will contain form data as well as uploaded files.
 * Inputs prefixed with 'data' will have the data prefix removed.  If there is
 * overlap between an input prefixed with data and one without, the 'data' prefixed
 * value will take precedence.
 *
 * @var array
 */
    public $data = array();

/**
 * Array of querystring arguments
 *
 * @var array
 */
    public $query = array();

/**
 * The url string used for the request.
 *
 * @var string
 */
    public $url;

/**
 * Base url path.
 *
 * @var string
 */
    public $base = false;

/**
 * webroot path segment for the request.
 *
 * @var string
 */
    public $webroot = '/';

/**
 * The full address to the current request
 *
 * @var string
 */
    public $here = null;

/**
 * The built in detectors used with `is()` can be modified with `addDetector()`.
 *
 * There are several ways to specify a detector, see CakeRequest::addDetector() for the
 * various formats and ways to define detectors.
 *
 * @var array
 */
    protected $_detectors = array(
        'get' => array('env' => 'REQUEST_METHOD', 'value' => 'GET'),
        'post' => array('env' => 'REQUEST_METHOD', 'value' => 'POST'),
        'put' => array('env' => 'REQUEST_METHOD', 'value' => 'PUT'),
        'delete' => array('env' => 'REQUEST_METHOD', 'value' => 'DELETE'),
        'head' => array('env' => 'REQUEST_METHOD', 'value' => 'HEAD'),
        'options' => array('env' => 'REQUEST_METHOD', 'value' => 'OPTIONS'),
        'ssl' => array('env' => 'HTTPS', 'value' => 1),
        'ajax' => array('env' => 'HTTP_X_REQUESTED_WITH', 'value' => 'XMLHttpRequest'),
        'flash' => array('env' => 'HTTP_USER_AGENT', 'pattern' => '/^(Shockwave|Adobe) Flash/'),
        'mobile' => array('env' => 'HTTP_USER_AGENT', 'options' => array(
            'Android', 'AvantGo', 'BlackBerry', 'DoCoMo', 'Fennec', 'iPod', 'iPhone','iPad',
            'J2ME', 'MIDP', 'NetFront', 'Nokia', 'Opera Mini', 'Opera Mobi', 'PalmOS', 'PalmSource',
            'portalmmm', 'Plucker', 'ReqwirelessWeb', 'SonyEricsson', 'Symbian', 'UP\\.Browser',
            'webOS', 'Windows CE', 'Xiino'
        ))
    );

/**
 * Constructor
 *
 * @param string $url Trimmed url string to use.  Should not contain the application base path.
 * @param boolean $parseEnvironment Set to false to not auto parse the environment. ie. GET, POST and FILES.
 */
    public function __construct($parseEnvironment = true) {
        $this->base = _BASE_;
        $this->webroot = _WEBROOT_;
        $url = $this->_url();
        
        if ($url[0] == '/') {
            $url = substr($url, 1);
        }
        $this->url = $url;

        if ($parseEnvironment) {
            $this->_processPost();
            $this->_processGet();
            $this->_processFiles();
        }
        $this->here = _BASE_ . $this->url;
    }

/**
 * process the post data and set what is there into the object.
 * processed data is available at $this->data
 *
 * @return void
 */
    protected function _processPost() {
        $this->data = $_POST;
        if (ini_get('magic_quotes_gpc') === '1') {
            $this->data = stripslashes_deep($this->data);
        }
        if (env('HTTP_X_HTTP_METHOD_OVERRIDE')) {
            $this->data['_method'] = env('HTTP_X_HTTP_METHOD_OVERRIDE');
        }
        if (isset($this->data['_method'])) {
            if (!empty($_SERVER)) {
                $_SERVER['REQUEST_METHOD'] = $this->data['_method'];
            } else {
                $_ENV['REQUEST_METHOD'] = $this->data['_method'];
            }
            unset($this->data['_method']);
        }
        $this->params['data'] = $this->data;

    }

/**
 * Process the GET parameters and move things into the object.
 *
 * @return void
 */
    protected function _processGet() {
        if (ini_get('magic_quotes_gpc') === '1') {
            $query = stripslashes_deep($_GET);
        } else {
            $query = $_GET;
        }
        $query = $_GET;
        
        unset($query['/' . $this->url]);
        if (strpos($this->url, '?') !== false) {
            list(, $querystr) = explode('?', $this->url);
            parse_str($querystr, $queryArgs);
            $query += $queryArgs;
        }
        if (isset($this->params['url'])) {
            $query = array_merge($this->params['url'], $query);
        }
        $this->query = $query;
    }

/**
 * Get the request uri.  Looks in PATH_INFO first, as this is the exact value we need prepared
 * by PHP.  Following that, REQUEST_URI, PHP_SELF, HTTP_X_REWRITE_URL and argv are checked in that order.
 * Each of these server variables have the base path, and query strings stripped off
 *
 * @return string URI The CakePHP request path that is being accessed.
 */
    protected function _url() {
        if (!empty($_GET['url'])) {
            $uri = $_GET['url'];
        } elseif (!empty($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['PHP_SELF']) && isset($_SERVER['SCRIPT_NAME'])) {
            $uri = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['PHP_SELF']);
        } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $uri = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif ($var = env('argv')) {
            $uri = $var[0];
        }

        $base = $this->base;

        if (strlen($base) > 0 && strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        if (strpos($uri, '?') !== false) {
            $uri = parse_url($uri, PHP_URL_PATH);
        }
        if (empty($uri) || $uri == '/' || $uri == '//') {
            return '/';
        }
        
        return $uri;
    }

    
/**
 * Process $_FILES and move things into the object.
 *
 * @return void
 */
    protected function _processFiles() {
        if (isset($_FILES) && is_array($_FILES)) {
            foreach ($_FILES as $name => $data) {
                if ($name != 'data') {
                    $this->params['form'][$name] = $data;
                }
            }
        }
    }

/**
 * Get the IP the client is using, or says they are using.
 *
 * @param boolean $safe Use safe = false when you think the user might manipulate their HTTP_CLIENT_IP
 *   header.  Setting $safe = false will will also look at HTTP_X_FORWARDED_FOR
 * @return string The client IP.
 */
    public function clientIp($safe = false) {
        if (!$safe && env('HTTP_X_FORWARDED_FOR') != null) {
            $ipaddr = preg_replace('/(?:,.*)/', '', env('HTTP_X_FORWARDED_FOR'));
        } else {
            if (env('HTTP_CLIENT_IP') != null) {
                $ipaddr = env('HTTP_CLIENT_IP');
            } else {
                $ipaddr = env('REMOTE_ADDR');
            }
        }

        if (env('HTTP_CLIENTADDRESS') != null) {
            $tmpipaddr = env('HTTP_CLIENTADDRESS');

            if (!empty($tmpipaddr)) {
                $ipaddr = preg_replace('/(?:,.*)/', '', $tmpipaddr);
            }
        }
        if($ipaddr == "::1") { // ipv6
        	$ipaddr = '127.0.0.1';
        }
        return trim($ipaddr);
    }

/**
 * Returns the referer that referred this request.
 *
 * @param boolean $local Attempt to return a local address. Local addresses do not contain hostnames.
 * @return string The referring address for this request.
 */
    public function referer($local = false) {
        $ref = env('HTTP_REFERER');
        $forwarded = env('HTTP_X_FORWARDED_HOST');
        if ($forwarded) {
            $ref = $forwarded;
        }

        $base = '';
        if (defined('FULL_BASE_URL')) {
            $base = FULL_BASE_URL . $this->webroot;
        }
        if (!empty($ref) && !empty($base)) {
            if ($local && strpos($ref, $base) === 0) {
                $ref = substr($ref, strlen($base));
                if ($ref[0] != '/') {
                    $ref = '/' . $ref;
                }
                return $ref;
            } elseif (!$local) {
                return $ref;
            }
        }
        return '/';
    }

/**
 * Missing method handler, handles wrapping older style isAjax() type methods
 *
 * @param string $name The method called
 * @param array $params Array of parameters for the method call
 * @return mixed
 * @throws CakeException when an invalid method is called.
 */
    public function __call($name, $params) {
        if (strpos($name, 'is') === 0) {
            $type = strtolower(substr($name, 2));
            return $this->is($type);
        }
        throw new HException(__('Method %s does not exist', $name));
    }

/**
 * Magic get method allows access to parsed routing parameters directly on the object.
 *
 * Allows access to `$this->params['controller']` via `$this->controller`
 *
 * @param string $name The property being accessed.
 * @return mixed Either the value of the parameter or null.
 */
    public function __get($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
        return null;
    }

/**
 * Check whether or not a Request is a certain type.  Uses the built in detection rules
 * as well as additional rules defined with CakeRequest::addDetector().  Any detector can be called
 * as `is($type)` or `is$Type()`.
 *
 * @param string $type The type of request you want to check.
 * @return boolean Whether or not the request is the type you are checking.
 */
    public function is($type) {
        $type = strtolower($type);
        if (!isset($this->_detectors[$type])) {
            return false;
        }
        $detect = $this->_detectors[$type];
        if (isset($detect['env'])) {
            if (isset($detect['value'])) {
                return env($detect['env']) == $detect['value'];
            }
            if (isset($detect['pattern'])) {
                return (bool)preg_match($detect['pattern'], env($detect['env']));
            }
            if (isset($detect['options'])) {
                $pattern = '/' . implode('|', $detect['options']) . '/i';
                return (bool)preg_match($pattern, env($detect['env']));
            }
        }
        if (isset($detect['callback']) && is_callable($detect['callback'])) {
            return call_user_func($detect['callback'], $this);
        }
        return false;
    }


    public function addParams($params) {
        $this->params = array_merge($this->params, (array)$params);
        return $this;
    }

/**
 * Add paths to the requests' paths vars.  This will overwrite any existing paths.
 * Provides an easy way to modify, here, webroot and base.
 *
 * @param array $paths Array of paths to merge in
 * @return CakeRequest the current object, you can chain this method.
 */
    public function addPaths($paths) {
        foreach (array('webroot', 'here', 'base') as $element) {
            if (isset($paths[$element])) {
                $this->{$element} = $paths[$element];
            }
        }
        return $this;
    }

/**
 * Get the value of the current requests url.  Will include named parameters and querystring arguments.
 *
 * @param boolean $base Include the base path, set to false to trim the base path off.
 * @return string the current request url including query string args.
 */
    public function here($base = true) {
        $url = $this->here;
        if (!empty($this->query)) {
            $url .= '?' . http_build_query($this->query);
        }
        if (!$base) {
            $url = preg_replace('/^' . preg_quote($this->base, '/') . '/', '', $url, 1);
        }
        return $url;
    }

/**
 * Read an HTTP header from the Request information.
 *
 * @param string $name Name of the header you want.
 * @return mixed Either false on no header being set or the value of the header.
 */
    public static function header($name) {
        $name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (!empty($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        return false;
    }

/**
 * Get the HTTP method used for this request.
 * There are a few ways to specify a method.
 *
 * - If your client supports it you can use native HTTP methods.
 * - You can set the HTTP-X-Method-Override header.
 * - You can submit an input with the name `_method`
 *
 * Any of these 3 approaches can be used to set the HTTP method used
 * by CakePHP internally, and will effect the result of this method.
 *
 * @return string The name of the HTTP method used.
 */
    public function method() {
        return env('REQUEST_METHOD');
    }

/**
 * Get the host that the request was handled on.
 *
 * @return void
 */
    public function host() {
        return env('HTTP_HOST');
    }

/**
 * Get the domain name and include $tldLength segments of the tld.
 *
 * @param integer $tldLength Number of segments your tld contains. For example: `example.com` contains 1 tld.
 *   While `example.co.uk` contains 2.
 * @return string Domain name without subdomains.
 */
    public function domain($tldLength = 1) {
        $segments = explode('.', $this->host());
        $domain = array_slice($segments, -1 * ($tldLength + 1));
        return implode('.', $domain);
    }

/**
 * Get the subdomains for a host.
 *
 * @param integer $tldLength Number of segments your tld contains. For example: `example.com` contains 1 tld.
 *   While `example.co.uk` contains 2.
 * @return array of subdomains.
 */
    public function subdomains($tldLength = 1) {
        $segments = explode('.', $this->host());
        return array_slice($segments, 0, -1 * ($tldLength + 1));
    }

/**
 * Find out which content types the client accepts or check if they accept a
 * particular type of content.
 *
 * #### Get all types:
 *
 * `$this->request->accepts();`
 *
 * #### Check for a single type:
 *
 * `$this->request->accepts('json');`
 *
 * This method will order the returned content types by the preference values indicated
 * by the client.
 *
 * @param string $type The content type to check for.  Leave null to get all types a client accepts.
 * @return mixed Either an array of all the types the client accepts or a boolean if they accept the
 *   provided type.
 */
    public function accepts($type = null) {
        $raw = $this->parseAccept();
        $accept = array();
        foreach ($raw as $value => $types) {
            $accept = array_merge($accept, $types);
        }
        if ($type === null) {
            return $accept;
        }
        return in_array($type, $accept);
    }

/**
 * Parse the HTTP_ACCEPT header and return a sorted array with content types
 * as the keys, and pref values as the values.
 *
 * Generally you want to use CakeRequest::accept() to get a simple list
 * of the accepted content types.
 *
 * @return array An array of prefValue => array(content/types)
 */
    public function parseAccept() {
        $accept = array();
        $header = explode(',', $this->header('accept'));
        foreach (array_filter($header) as $value) {
            $prefPos = strpos($value, ';');
            if ($prefPos !== false) {
                $prefValue = substr($value, strpos($value, '=') + 1);
                $value = trim(substr($value, 0, $prefPos));
            } else {
                $prefValue = '1.0';
                $value = trim($value);
            }
            if (!isset($accept[$prefValue])) {
                $accept[$prefValue] = array();
            }
            if ($prefValue) {
                $accept[$prefValue][] = $value;
            }
        }
        krsort($accept);
        return $accept;
    }

/**
 * Get the languages accepted by the client, or check if a specific language is accepted.
 *
 * Get the list of accepted languages:
 *
 * {{{ CakeRequest::acceptLanguage(); }}}
 *
 * Check if a specific language is accepted:
 *
 * {{{ CakeRequest::acceptLanguage('es-es'); }}}
 *
 * @param string $language The language to test.
 * @return If a $language is provided, a boolean. Otherwise the array of accepted languages.
 */
    public static function acceptLanguage($language = null) {
        $accepts = preg_split('/[;,]/', self::header('Accept-Language'));
        foreach ($accepts as &$accept) {
            $accept = strtolower($accept);
            if (strpos($accept, '_') !== false) {
                $accept = str_replace('_', '-', $accept);
            }
        }
        if ($language === null) {
            return $accepts;
        }
        return in_array($language, $accepts);
    }


    public function offsetGet($name) {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }
        if ($name == 'url') {
            return $this->query;
        }
        if ($name == 'data') {
            return $this->data;
        }
        return null;
    }


    public function offsetSet($name, $value) {
        $this->params[$name] = $value;
    }


    public function offsetExists($name) {
        return isset($this->params[$name]);
    }


    public function offsetUnset($name) {
        unset($this->params[$name]);
    }
}
