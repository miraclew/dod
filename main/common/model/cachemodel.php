<?php
/**
 * 缓存模型
 * 
 * 自动缓存数据库中的数据
 */
class CacheModel extends Model {
	
	// TODO 暂不支持id 数组
	public static function findByPk($id) {
		$model = null;
		$cls = get_called_class();
		$key = 'modelcache:'.$cls.':'.$id;
		$cache = RedisHashModel::redis()->hGetAll($key);
		if ($cache) {
			$model = new $cls($cache);
		}
		else {
			$model = parent::findByPk($id);
			RedisHashModel::redis()->hSetAll($key, $model->attributes()); 
		}
		
		return $model;
	}
	
	public function save($validate=true) 
	{
		if (!$this->is_new_record() && $this->is_dirty()) {
			$cls = get_called_class();
			$key = 'modelcache:'.$cls.':'.$this->id;
			RedisHashModel::redis()->hSetAll($key, $this->attributes()); 			
		}

		parent::save($validate);
	}
}