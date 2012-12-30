<?php

//注册应用层的自动类加载
$classes = array(
	'ApiController'             => 'main/api/controller/apicontroller.php',
	'CommonApiController'       => 'main/api/controller/commonapicontroller.php',
	'CallbackApiController'     => 'main/api/controller/callbackapicontroller.php',
		
	'AccountApiController'      => 'main/api/controller/accountapicontroller.php',	
	'UserApiController'        	=> 'main/api/controller/userapicontroller.php',        
	'RelationApiController'		=> 'main/api/controller/relationapicontroller.php',		
    'RoomApiController'    		=> 'main/api/controller/roomapicontroller.php',
	'TalkApiController'    		=> 'main/api/controller/talkapicontroller.php',
	'CommentApiController'    	=> 'main/api/controller/commentapicontroller.php',		
    'MessageApiController'     	=> 'main/api/controller/messageapicontroller.php',
    'PhotoApiController'		=> 'main/api/controller/photoapicontroller.php',
	'SearchApiController'		=> 'main/api/controller/searchapicontroller.php',
	'FavoriteApiController'		=> 'main/api/controller/favoriteapicontroller.php',
	'InterviewApiController'	=> 'main/api/controller/interviewapicontroller.php',
	'ItemApiController'			=> 'main/api/controller/itemapicontroller.php',
	'ReportApiController'		=> 'main/api/controller/reportapicontroller.php',
	'ShareApiController'		=> 'main/api/controller/shareapicontroller.php',
	'TaskApiController'			=> 'main/api/controller/taskapicontroller.php',
	'BlockApiController'		=> 'main/api/controller/blockapicontroller.php',
	'ServerApiController'		=> 'main/api/controller/serverapicontroller.php',
	'ResourceApiController'		=> 'main/api/controller/resourceapicontroller.php',
	'StatusApiController'		=> 'main/api/controller/statusapicontroller.php',
    
	'MessageQueue'              => 'includes/util/messagequeue.php',

    'OpenApi'                   => 'main/lib/third/openapi/api_interface.php',
    'OpenApiInterface'          => 'main/lib/third/openapi/api_interface.php',
    'Utility'                   => 'main/lib/utility.php',
    'FileConverter'             => 'main/lib/fileconverter.php',
    'ErrRtnException'           => 'main/lib/exception.php',
	'Logger'                   	=> 'main/lib/third/log4php/Logger.php',
	'UsersApi'                  => 'main/lib/usersapi.php',
	'UsersThirdApi'             => 'main/lib/usersthirdapi.php',

	'ApnsPush'            		=> 'main/lib/apns.php',
	'Image'            			=> 'main/lib/image.php',
	'Voice'            			=> 'main/lib/voice.php',

	//后台管理api接口
    'AdminApiController'        => 'main/adminapi/controller/adminapicontroller.php',
	'RoomAdminapiController'    => 'main/adminapi/controller/roomadminapicontroller.php',
	'AccountAdminapiController' => 'main/adminapi/controller/accountadminapicontroller.php',
	'ReportAdminapiController' => 'main/adminapi/controller/reportadminapicontroller.php',
	'MessageAdminapiController' => 'main/adminapi/controller/messageadminapicontroller.php',
	
	// WEB 页面
	'ShareWebController' 		=> 'main/web/controller/sharewebcontroller.php'
);

AutoLoader::register($classes);