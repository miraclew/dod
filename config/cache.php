<?php

$engine = 'File';
if (extension_loaded('apc') && function_exists('apc_dec') && ini_get('apc.enabled')  && (php_sapi_name() !== 'cli' || ini_get('apc.enable_cli'))) {
    $engine = 'Apc';
}
setConfig("cacheengine", $engine);//if engine is File ,then getCacheKey transform : to _
// In development mode, caches should expire quickly.
$duration = 3600 * 36; //36个小时
if (config('debug') >= 1) {
    $duration = 1000;
}

Cache::config('_hd_core_', array(
    'engine' => $engine,
    'prefix' => 'hd_core_',
    'path' => CACHE_ . 'persistent' . DS,
    'serialize' => ($engine === 'File'),
    'duration' => $duration
));

Cache::config('_hd_model_', array(
    'engine' => $engine,
    'prefix' => 'hd_model_',
    'path' => CACHE_ . 'persistent' . DS,
    'serialize' => ($engine === 'File'),
    'duration' => $duration
));

/*Cache::config('default1', array(
    'engine' => 'Redis',
    'servers' => '192.168.1.196',
    //'prefix' => 'p_',
    'duration' => $duration
));*/

Cache::config('default', array(
    'engine' => 'Memcache',
    'servers' => '127.0.0.1:11211',
    'prefix' => '',
    'duration' => $duration
));

/*
Cache::config('default1', array(
    'engine' => 'Redis',
    'servers' => '192.168.1.196',
    //'prefix' => 'p_',
    'duration' => $duration
));*/

// app critical data, need backup 
Cache::config('aloha', array(
    'engine' => 'Redis',
    'servers' => 'localhost:6379',
    //'prefix' => 'p_',
    'duration' => $duration
));

// app cache data, backup not needed 
Cache::config('aloha_cache', array(
'engine' => 'Redis',
'servers' => 'localhost:6379',
//'prefix' => 'p_',
'duration' => $duration
));

Cache::config('aloha_job_queue', array(
'engine' => 'Redis',
'servers' => 'localhost:6379',
//'prefix' => 'p_',
'duration' => $duration
));

