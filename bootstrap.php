<?php
// import depend libs
require_once 'lib/ar/ActiveRecord.php';

// import core
require_once 'framework/Model.php';

// import config
$config = array();
$config['database'] = include 'config/database.php';

// initialize ActiveRecord
ActiveRecord\Config::initialize(function($cfg)
{
	$cfg->set_model_directory('.');
	$cfg->set_connections(array('development' => 'mysql://root:fw123456@127.0.0.1/dod'));
});


class User extends Model { }

echo "<pre>";
print_r(User::first()->created);
echo "</pre>";