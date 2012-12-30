<?php

include_once MAIN_ . 'autoload.php';
include_once MAIN_ . 'globaldef.php';
include_once MAIN_ . 'app.php';
require_once MAIN_ . 'api.php';
require_once MAIN_ . 'const.php';
//require_once MAIN_ . 'error.php';

//加载语言相关的错误描述
$language = config('language', 'chi');
$langErrFile = MAIN_. 'locale/'.$language.'/errmsg.php';
if (file_exists($langErrFile)) {
    include_once($langErrFile);
} else {
    throw new HException('no language file');
}

$localeFile = MAIN_ . 'locale/'.$language.'/locale.php';
if (file_exists($localeFile)) {
    include_once($localeFile);
} else {
    throw new HException('no locale file');
}


// 加载异步队列
require_once MAIN_.'lib/resque/Resque.php';
$settings = Cache::settings('aloha_job_queue');
Resque::setBackend($settings['servers'][0]);

// register system level event listeners
App::instance()->initialize();


// 只给指定用户输出debug信息 TODO move to other place
function udebug($accountid, $var) {
	if (Auth::user('accountid') == $accountid) {
		$calledFrom = debug_backtrace();
        $file = substr(str_replace(ROOT_, '', $calledFrom[0]['file']), 1);
        $line = $calledFrom[0]['line'];
        echo "<p>$file:$line</p>";        
        echo "<pre>".print_r($var, true)."</pre>";
	}
}
