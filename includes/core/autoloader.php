<?php
/**
 * This defines autoloading handler, refer to MediaWiki
 */
global $autoloadClasses;
$autoloadClasses = array(
    //core
    'Object'            => 'includes/core/object.php',
    'ObjectFactory'     => 'includes/core/objectfactory.php',
    'Request'           => 'includes/core/request.php',
    'Response'          => 'includes/core/response.php',
    'Dispatcher'        => 'includes/core/dispatcher.php',
    'Router'            => 'includes/core/router.php',

    //Cache
    'Cache'             => 'includes/cache/cache.php',
    'ApcEngine'         => 'includes/cache/engine/apcengine.php',
    'FileEngine'        => 'includes/cache/engine/fileengine.php',
    'RedisEngine'       => 'includes/cache/engine/redisengine.php',
    'memcacheengine'    => 'includes/cache/engine/memcacheengine.php',

    //db
    'DboSource'         => 'includes/db/dbosource.php',
    'DataSource'        => 'includes/db/datasource.php',
    'DBManager'         => 'includes/db/dbmanager.php',
    'Mysql'             => 'includes/db/mysql.php',

    //mvc
    'View'              => 'includes/core/view.php',
    //'Model'             => 'includes/core/model.php',
    'Controller'        => 'includes/core/controller.php',
		
	//model 
	'Table'           	=> 'includes/model/table.php',
	'Model'           	=> 'includes/model/model.php',
	'ModelValidations' 	=> 'includes/model/validations.php',
	'ModelErrors' 		=> 'includes/model/validations.php',
		
	//event 
	'Event'				=> 'includes/event/event.php',
	'EventListener'		=> 'includes/event/eventlistener.php',
	'EventManager'		=> 'includes/event/eventmanager.php',

    //session
    'Session'           => 'includes/helper/session.php',
    'MysqlSession'      => 'includes/helper/session/mysqlsession.php',
    'CacheSession'      => 'includes/helper/session/cachesession.php',
    'SessionHandlerInterface' => 'includes/helper/session.php',
    
    //helper
    'Auth'              => 'includes/helper/auth.php',
    'Validator'         => 'includes/helper/validator.php',
    'HttpRequest'       => 'includes/helper/httprequest.php',
	'Html'				=> 'includes/helper/html.php',
	'Form'				=> 'includes/helper/form.php',

    //Utility
    'String'            => 'includes/util/string.php',
    'File'              => 'includes/util/file.php',

    //log
    'Log'               => 'includes/log/log.php',
    'FileLog'           => 'includes/log/filelog.php',
    'LogInterface'      => 'includes/log/loginterface.php',
);

class AutoLoader {
	private static $classes = array();
    /**
     * autoload - take a class name and attempt to load it
     *
     * @param $className String: name of class we're looking for.
     * @return bool Returning false is important on failure as
     * it allows Zend to try and look in other registered autoloaders
     * as well.
     */
    static function autoload($className) {
        global $autoloadClasses;

        if (isset($autoloadClasses[$className])) {
            $filename = $autoloadClasses[$className];
        } else if (isset(self::$classes[$className])) {
        	$filename = self::$classes[$className];
        }else {
            # Try a different capitalisation
            # The case can sometimes be wrong when unserializing PHP 4 objects
            $filename = false;
            $lowerClass = strtolower( $className );

            foreach ($autoloadClasses as $class2 => $file2) {
                if (strtolower($class2) == $lowerClass) {
                    $filename = $file2;
                }
            }

            if (!$filename) {
            	$include_path = get_include_path();
				$include_path .= PATH_SEPARATOR.MAIN_."common/model";
				$include_path .= PATH_SEPARATOR.MAIN_."common/model/redis";
				$include_path .= PATH_SEPARATOR.MAIN_."component";
				set_include_path($include_path);
				
				if (stripos($lowerClass, 'predis') === false) {
					$ret = require_once $lowerClass.".php";;
					
					if ($ret && is_subclass_of($className, 'Model')) {
						//$className::initialize();
					}
					
					return $ret;
				}
            	
				return false;
            }
        }

        # Make an absolute path, this improves performance by avoiding some stat calls
        $filename = ROOT_ . $filename;
        if (!file_exists($filename)) {
        	//TODO error handler
        }

        require_once($filename);
        return true;
    }
    
    //注册加载文件，$file为相对于root的路径
    static function register($class, $file = null) {
    	//TODO 需要检查这里的实现
    	if (is_array($class)) {
    		foreach ($class as $k => $v) {
    		    $filename = $v;
                self::$classes[$k] = $filename;
    		}
    		return;
    	}
    	$this->classes[$class] = $file;
    }
    
    static function load($class) {
    	if (isset(self::$classes[$class])) {
            $filename = ROOT_ . self::$classes[$class];
            if (file_exists($filename)) {
               include_once($filename);
               return true;
            }
        }
        return false;
    }

}

if ( function_exists( 'spl_autoload_register' ) ) {
    spl_autoload_register( array( 'AutoLoader', 'autoload' ) );
} else {
    function __autoload( $class ) {
        AutoLoader::autoload( $class );
    }

    ini_set( 'unserialize_callback_func', '__autoload' );
}
