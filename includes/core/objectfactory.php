<?php


class ObjectFactory {
    protected static $_objects = array();
    public static function getObject($classname, $setting = null) {
        if (isset(self::$_objects[$classname])) {
            return self::$_objects[$classname];
        }
        $instance = new $classname($setting);
        if (isset($instance)) {
            self::$_objects[$classname] = $instance;
            return $instance;
        }
        return false;
    }
}

