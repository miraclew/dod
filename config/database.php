<?php
define('HOST', '114.80.108.213');
define('PORT', '63306');
define('PASSWORD' , 'hd7803%f');

//数据库配置，可以通过DBManager::instance()->getDbConfig()获取该类的实例  
class DBConfig {
    
    public $user = array(
        'datasource' => 'Mysql',
        'persistent' => false,
        'host' => HOST,
		'port' => PORT,
        'login' => 'root',
        'password' => PASSWORD,
        'database' => 'qyh_user',
        'prefix' => '',
        'encoding' => 'utf8',
    );
    
    public $relation = array(
        'datasource' => 'Mysql',
        'persistent' => false,
        'host' => HOST,
		'port' => PORT,
        'login' => 'root',
        'password' => PASSWORD,
        'database' => 'qyh_relation',
        'prefix' => '',
        'encoding' => 'utf8',
    );
    
    public $room = array(
        'datasource' => 'Mysql',
        'persistent' => false,
        'host' => HOST,
		'port' => PORT,
        'login' => 'root',
        'password' => PASSWORD,
        'database' => 'qyh_room',
        'prefix' => '',
        'encoding' => 'utf8',
    );
    
    public $log = array(
        'datasource' => 'Mysql',
        'persistent' => false,
        'host' => HOST,
		'port' => PORT,
        'login' => 'root',
        'password' => PASSWORD,
        'database' => 'qyh_log',
        'prefix' => '',
        'encoding' => 'utf8',
    );
    
    public $message = array(
    		'datasource' => 'Mysql',
    		'persistent' => false,
    		'host' => HOST,
			'port' => PORT,
    		'login' => 'root',
    		'password' => PASSWORD,
    		'database' => 'qyh_message',
    		'prefix' => '',
    		'encoding' => 'utf8',
    );
    
    public $system = array(
    		'datasource' => 'Mysql',
    		'persistent' => false,
    		'host' => HOST,
			'port' => PORT,
    		'login' => 'root',
    		'password' => PASSWORD,
    		'database' => 'qyh_system',
    		'prefix' => '',
    		'encoding' => 'utf8',
    );
	
    public $store = array(
    		'datasource' => 'Mysql',
    		'persistent' => false,
    		'host' => HOST,
			'port' => PORT,
    		'login' => 'root',
    		'password' => PASSWORD,    		
    		'database' => 'qyh_store',
    		'prefix' => '',
    		'encoding' => 'utf8',
    );
	
    
}
