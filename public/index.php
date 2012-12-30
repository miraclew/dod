<?php
//echo phpinfo();die;
//echo "hello,public/index.php";
//debug(111);
//初始化运行环境，使用相对路径
require(__DIR__.'/../includes/setup.php');

global $hdRequest;
global $hdResponse;

//Session::instance()->start();
$hdRequest = new Request();
$hdResponse = new Response(array('charset' => config('encoding')));
$dispatcher = new Dispatcher();

$dispatcher->dispatch($hdRequest, $hdResponse);

//Session::instance()->close();


