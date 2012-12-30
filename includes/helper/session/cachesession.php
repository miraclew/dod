<?php

class CacheSession implements SessionHandlerInterface {

    public function __construct($config) {
        
    }
    
    public function open() {
        return true;
    }

    public function close() {
        $probability = mt_rand(1, 150);
        if ($probability <= 3) {
            Cache::gc();
        }
        return true;
    }

    public function read($id) {
        return Cache::read($id, config('session.handler.config'));
    }

    public function write($id, $data) {
        return Cache::write($id, $data, null, config('session.handler.config'));
    }

    public function destroy($id) {
        return Cache::delete($id, config('session.handler.config'));
    }


    public function gc($expires = null) {
        return Cache::gc();
    }

    public function __destruct() {
        session_write_close();
    }
}