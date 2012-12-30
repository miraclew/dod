<?php
// directory const define
define('ROOT', __DIR__);

// php settings
@ini_set("error_reporting", E_ALL);
@ini_set("display_errors", 'On');
@ini_set("error_log", ROOT.'/tmp/logs/php_error.log');

date_default_timezone_set('Asia/Shanghai');

// import depend libs
require_once ROOT.'/lib/ar/ActiveRecord.php';

// import core
require_once ROOT.'/framework/Utility.php';
require_once ROOT.'/framework/Model.php';

// import config
$config = array();
$config['database'] = include ROOT.'/config/database.php';

// initialize ActiveRecord
ActiveRecord\Config::initialize(function($cfg)
{
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => 'mysql://root:apple@127.0.0.1/dod'));
});

// setup autoload
$include_path = get_include_path();
$include_path .= PATH_SEPARATOR.ROOT.'/app/model';
set_include_path($include_path);

function model_autoload($class_name){
	$class_name = strtolower($class_name);
	require_once ROOT.'/app/model/'.$class_name.".php";
}

spl_autoload_register('model_autoload');

// route
$controller = $_REQUEST['c'];
$action = $_REQUEST['a'];
if (empty($controller) || empty($action)) {
	die("params required");
}

$controller_file = ROOT.'/app/controller/'.ucfirst($controller).'.php';
if (!file_exists($controller_file)) {
	die("controller $controller not found, file not exist");
}

require_once $controller_file;
$cls = ucfirst($controller).'Controller';
$c = new $cls;

if (!class_exists($cls)) {
	die("controller $controller not found, class not exist");
}

if (!method_exists($c, $action)) {
	die("action $action not found");
}

$c->$action();
