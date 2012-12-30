<?php
/**
* 系统推荐关注
*/
class RecommendFollowing extends Model {
	public static $useTable = 'recommend_followings';
	public static $useDbConfig = 'relation';
	
	public static function random() {
		$all = self::queryBySql("select * from qyh_relation.recommend_followings order by rand() limit 4");
		$data = Utility::collectField($all, 'accountid');
		return $data;
	}	
}