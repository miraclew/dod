<?php

class MysqlSession implements SessionHandlerInterface {
    protected $_db;
    protected $_table;
    protected $_timeout;

    public $cacheEnabled = true;
    public $cacheDuration = 1800; //30分钟
    public $cacheConfig = 'default'; //memcache缓存    
    const CACHEKEY_SESSION_S = "session_%s";
    
    public function __construct($setting) {
        if (empty($setting['dbconfig']))
            throw new HException('dbconfig missing for mysql session config');
        $this->_db = DBManager::instance()->getDataSource($setting['dbconfig']);
        if (empty($this->_db))
            throw new HException('can not establish mysql session for '.$setting['dbconfig']);
        $this->_table = isset($setting['table']) ? $setting['table'] : 'sessions';
        $this->_timeout = config('session.timeout');
        if (empty($this->_timeout)) $this->_timeout = 3600 * 24 * 90;
    }
    
    public function open() {
        return true;
    }

    public function close() {
        $probability = mt_rand(1, 150);
        if ($probability <= 3) {
            $this->gc();
        }
        return true;
    }

    public function read($id) {
    	$key = sprintf(self::CACHEKEY_SESSION_S, $id); 
	    $cache = Cache::read($key, $this->cacheConfig);
    	if ($cache !== false){ //缓存命中
    		return $cache;
    	}
    	$expires = time();
        $sql = "SELECT data from $this->_table where id = ? and expiretime >= ?";
        $data = $this->_db->query($sql, array($id, $expires));
        
        if (empty($data) || empty($data[0]['data']))
            return false;
        $cache = $data[0]['data'];
        Cache::write($key, $cache, $this->cacheDuration, $this->cacheConfig);
        return $data[0]['data'];
        
    }

    public function write($id, $data) {
        if (!$id) {
            return false;
        }

        $sql = "SELECT data from $this->_table where id = ?";
        $expires = time() + $this->_timeout;
        if ($this->_db->query($sql, array($id))) {
            $sql = "UPDATE $this->_table SET data=?, expiretime = ? WHERE id = ?";
            $this->_db->query($sql, array($data, $expires, $id));
        } else {
        	$sData = Session::unserialize(session_encode());
	        if (isset($sData[auth::$sessionKey]['accountid'])) {
	        	$accountId = $sData[auth::$sessionKey]['accountid'];
	            $sql = "INSERT INTO $this->_table (id, data, expiretime, accountid) VALUES (?, ?, ?, ?)";
	            $this->_db->query($sql, array($id, $data, $expires, $accountId));
	        }
	        /*else {
                $sql = "INSERT INTO $this->_table (id, data, expiretime) VALUES (?, ?, ?)";
                $this->_db->query($sql, array($id, $data, $expires));
	        }*/
        }
        $key = sprintf(self::CACHEKEY_SESSION_S, $id);
        Cache::write($key, $data, $this->cacheDuration, $this->cacheConfig);
        return $this->_db->lastAffected() > 0;
    }

    public function destroy($id) {
        $sql = "DELETE FROM $this->_table WHERE id = ?";
        $this->_db->query($sql, array($id));
        
        $key = sprintf(self::CACHEKEY_SESSION_S, $id);
        Cache::delete($key, $this->cacheConfig);
        return $this->_db->lastAffected() > 0;
    }


    public function gc($expires = null) {
        if (!$expires) {
            $expires = time();
        }
        //TODO 删除cache
        $sql = "DELETE FROM $this->_table WHERE expiretime < ?";
        $result = $this->_db->query($sql, array($expires));
        return $result !== false;
        
    }

    public function __destruct() {
        session_write_close();
    }
}