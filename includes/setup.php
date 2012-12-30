<?php
//////////////////////////////////////////////////////////////////////
// Web Setup
//////////////////////////////////////////////////////////////////////

//时间相关
define('TIME_START', microtime(true));

if (PHP_SAPI === 'cgi')
    define('CGIMODE', true);
else
    define('CGIMODE', false);

//定义目录, 末尾加'_'的，代表路径最后有斜杠
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_', dirname(dirname(__FILE__)) . '/');
define('INCLUDES_', ROOT_ . 'includes/');
define('PUBLIC_', ROOT_ . 'public/');
define('MAIN_', ROOT_ . 'main/');
define('TEST_', ROOT_ . 'test/');
define('CONFIG_', ROOT_ . 'config/');
define('TMP_', ROOT_ . 'tmp/');
define('LOG_', TMP_ . 'log/');
define('CACHE_', TMP_ . 'cache/');

// 定义相对名称，以'_'开头
// 定义base
$base = dirname($_SERVER['SCRIPT_NAME']);
if (basename($base) === 'public') {
    $base = dirname($base);
    if ($base === DS || $base ==='.' || $base === '/') {
        $base = '';
    }
} else {
    $base = '';
}
define('_BASE', $base);
define('_BASE_', _BASE . '/');
define('_WEBROOT_', _BASE_);

$isWin = DS === '\\' ? true : false;
define('IS_WIN', $isWin);
    
require(INCLUDES_ . 'util/function.php');   //load the basic functions
require(INCLUDES_ . 'util/debug.php');
require(INCLUDES_ . 'error/errorhandler.php');
require(INCLUDES_ . 'error/exception.php');
set_error_handler('ErrorHandler::handleError', E_ALL & ~E_DEPRECATED);
set_exception_handler('ErrorHandler::handleException');
require(INCLUDES_ . 'core/autoloader.php'); //register autoloader
require(CONFIG_ . 'config.php');    //load all common configs
require(CONFIG_ . 'deploy.php');     //load all deployment configs
require(CONFIG_ . 'database.php');    //load all database configs
require(CONFIG_ . 'cache.php');     //local all cache configs

//error_reporting(E_ALL & ~E_DEPRECATED);
if (config('debug')){
    @ini_set("error_reporting", E_ALL);
    @ini_set("display_errors", TRUE);
} else {
    @ini_set("error_reporting", 0);
    @ini_set("display_errors", false);
}

//load logic module
require(MAIN_ . 'init.php');

date_default_timezone_set('Asia/Shanghai');

// register_shutdown_function('handleShutdown');//When using php-fpm, fastcgi_finish_request() should be used instead of register_shutdown_function() and exit()
// function handleShutdown() {       
// 	define('E_FATAL',  E_ERROR | E_USER_ERROR | E_PARSE | E_CORE_ERROR |         E_COMPILE_ERROR | E_RECOVERABLE_ERROR); 
// 	$error = error_get_last();        
// 	if($error && ($error['type'] & E_FATAL)){           
// 		$info = "[SHUTDOWN] file:".$error['file']." | ln:".$error['line']." | msg:".$error['message'] .PHP_EOL;            
// 		Log::writeFile($info , 'fatalerror');        
// 	}        
//  }

