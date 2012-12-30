<?php
//this file will be maintained by maintenance deparment
global $dlConfig;
$dlConfig = array();

$dlConfig['clientversion']      = '0.7.8.1';
$dlConfig['updateurl']      = 'itms-services://?action=download-manifest&amp;url=http://dev.hoodinn.com/releases/plist/venus.plist';

$dlConfig['client_version_1']      = '0.5.1';//apple 越狱版
$dlConfig['update_url_1']      = 'itms-services://?action=download-manifest&amp;url=http://aloha.hellopix.net/releases/alohaupdate.plist';
$dlConfig['client_version_2']      = '0.5.2';//apple 非越狱版
$dlConfig['update_url_2']      = 'https://itunes.apple.com/cn/app/tai-gang/id553114436?mt=8';

$dlConfig['admin_ip']           = '192.168.1.0/24,218.80.246.6/32,127.0.0.1/32,::1';
//$dlConfig['log_path'] = 'd:\\work';//directory of log file

$dlConfig['TEST_DOMAIN']  = 1;//comment on this line on production machine 生产环境下请注释掉本行
if (isset($dlConfig['TEST_DOMAIN'])) { //if on development machine
	define('CALLBACK_URL',  'http://dev.hoodinn.com/aloha/api/callback');//回调url
	define('HTTP_PATH',  'http://dev.hoodinn.com/aloha/');//上传的文件路径。uploaded file path
	define('APP_SECRET',  'db40ad62a5d005fb6146d3da0911d03f');//user service secret
	define('USER_SERVICE_HOST',  '114.80.108.213');//user service ip
	define('USER_SERVICE_PORT',  16000);//user service port
	define('USER_SERVICE_THIRD_HOST',  '114.80.108.213');//user third service ip
	define('USER_SERVICE_THIRD_PORT',  16001);//user third service port
	define('IMG_STORAGE_ROOT_', PUBLIC_);
	define('VOICE_STORAGE_ROOT_', PUBLIC_);
	define('RESOURCE_ROOT_', PUBLIC_);	
}
else { //if on production machine
	define('USER_SERVICE_BASEURL',  'http://user.hoodinn.com:8080/');//用户中心服务url
	define('CALLBACK_URL',  'http://api.htapp.cn/callback');//回调url
	define('HTTP_PATH',  'http://f.htapp.cn/');//上传的文件路径。uploaded file path	
	define('APP_SECRET',  '3f0a4452983dae5c0a6db68dfb334081');//user service secret
	define('IMG_STORAGE_ROOT_', PUBLIC_);
	define('VOICE_STORAGE_ROOT_', PUBLIC_);
	define('RESOURCE_ROOT_', PUBLIC_);
}