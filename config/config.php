<?php

global $hdConfig;
$hdConfig = array();
$hdConfig['debug']              = 2;//set this value to 0 on production machine,set to 2 on development machine
$hdConfig['disablecache']       = 0;
$hdConfig['encoding']           = 'utf-8';
$hdConfig['prefix']             = array('api', 'adminapi', 'web');
$hdConfig['gzip']               = 0;
$hdConfig['app']['pointstoask'] = 1000;
$hdConfig['app']['duration']    = 24*3600;
$hdConfig['clientversion']      = '0.7.1';
$hdConfig['updateurl']      = 'itms-services://?action=download-manifest&amp;url=http://dev.hoodinn.com/releases/venus/venus.plist';

//security related
$hdConfig['securitysalt']       = 'EYhG93b0qyJfzxfs2guVoIubWwvniR2G0FgaC9mi';
$hdConfig['session']            = array(
    'handler'       => 'Mysql',
    'option'        => array('dbconfig' => 'system', 'table' => 'sessions'),
    'timeout'       => 3600*24*90,
);

$hdConfig['admin_ip']           = '192.168.1.0/24,218.80.246.6/32,127.0.0.1/32';

$hdConfig['TEST_DOMAIN']  = 1;//comment on this line on production machine 生产环境下请注释掉本行

//定义应用的模块，每个模块可以有单独的public路径
$hdConfig['modules']            = array(
    'api'           => array(
        'root_'     => MAIN_.'api/',
        'public_'   => MAIN_.'api/public/',
    ),
    'admin'         => array(
        'root_'     => MAIN_.'admin/',
        'public_'   => MAIN_.'admin/public/',
    ),
    'web'         => array(
        'root_'     => MAIN_.'web/',
        'public_'   => MAIN_.'web/public/',
    ),
	'test'          => array(
			'root_'     => MAIN_.'test/',
			'public_'   => MAIN_.'test/public/',
	),
);

$hdConfig['CountPerPage']       = 4;

// apns
$hdConfig['apns_env'] = 1; // 0: production, 1: sandbox
$hdConfig['apns_cert_file'] = __DIR__.'/cert/iostest_push_dev.pems'; //

//后台登录的账户配置
$hdConfig['admin_auth'] = array(
                                'hoodinn_aloha' => '123456',
                              );
