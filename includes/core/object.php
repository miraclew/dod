<?php

class Object {
    public $uses = false;
/**
 * Stop execution of the current script.  Wraps exit() making
 * testing easier.
 *
 * @param integer|string $status see http://php.net/exit for values
 * @return void
 */
    
    public function __construct() {
        
    }

    public function __isset($name) {
        if (is_array($this->uses) && in_array($name, $this->uses)) {
            return $this->loadModel($name);
        }
    }
    
    public function __get($name) {
        if (isset($this->{$name}))
            return $this->{$name};
        return null;
    }
    
    public function __set($name, $value) {
        return $this->{$name} = $value;
    }
    
    public function loadModel($modelClass = null) {
        if (!is_array($modelClass)) {
            if (strpos($modelClass, ',') !== false) {
                $modelClass = explode(',', $modelClass);
            } else {
                $modelClass = array($modelClass);
            }
        }
        
        
        foreach($modelClass as $model) {
            if (!isset($this->{$model})) {
                $this->{$model} = ObjectFactory::getObject($model);
            }
            if (!$this->{$model}) {
                throw new MissingModelException($model);
            }
        }
        return true;
    }
    
    protected function _stop($status = 0) {
        exit($status);
    }
}