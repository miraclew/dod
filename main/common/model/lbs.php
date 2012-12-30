<?php
/**
 * 位置服务类
 */
class LBS {
	const EARTH_RADIUS = 6371; // km
	const KM_PER_DEGREE = 111.12; //km
	const MAX_LIMIT = 1000;
	
	const GOOGLE_API_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=%s,%s&sensor=false&language=zh-CN';
	/**
	 * 反向地理位置编码 (经纬度转地理位置) 
	 */
	public function revGeoCode($lat, $lng) {
		$url = sprintf(self::GOOGLE_API_GEOCODE_URL, $lat, $lng);
		$req = new HttpRequest();
		$resp = $req->get($url, $header=array(), $timeout=10);
		if ($resp === false) {
			throw new ErrRtnException(Err::$FAIL);
		}
		$data = json_decode($resp, true);
		
		$locality = '';
		if(strtolower($data['status']) == 'ok') {
			$ac = $data['results'][0]['address_components'];
			$locality = $this->extractAddressComponents($ac, 'locality');
			$locality .= ' '.$this->extractAddressComponents($ac, 'sublocality');
		}
		return $locality;
	}

	private function extractAddressComponents($addressComponents, $component) {
		foreach ($addressComponents as $value) {
			if($value['types'][0] == $component)
				return $value['long_name']; 
		}
		return '';
	}
	
	/**
	 * 计算距离
	 */
	public function calcDistance($lat1, $lng1, $lat2, $lng2) {
		$radLat1 = $this->rad($lat1);
		$radLat2 = $this->rad($lat2);
		$a = $radLat1 - $radLat2;
		$b = $this->rad($lng1) - $this->rad($lng2);
		$s = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));
		$s = $s *self::EARTH_RADIUS;
		$s = round($s * 10000) / 10000;
		return $s;
	}
	
	// 求弧度
	private function rad($d) {
		return $d * 3.1415926535898 / 180.0;
	}
	
	public static function validatLatlng($lat, $lng) {
		return floatval($lat) < 90 && floatval($lat) > -90 &&  floatval($lng) < 180 && floatval($lng) > -180;
	}
	
	public static function setUserLocation($accountId, $lat, $lng, $location) {		
		$data['_id'] = $accountId;
		$data['loc'] = array(floatval($lng), floatval($lat));
		$data['updated'] = new MongoDate(time());
		$m = new Mongo();
		$db = $m->pois;
		$pois = $db->users;
		
		$result = $pois->save($data);
		return $result;
	}
	
	public function getUsersAroundMe($accountId, $page=1, $pageLimit=200, $maxDistance=0) {
		$lat = floatval(UserCounter::getInfo($accountId)->lat);
		$lng = floatval(UserCounter::getInfo($accountId)->lng);
		
		if (!LBS::validatLatlng($lat, $lng)) return array();
			
		// $con = new Mongo("mongodb://{$username}:{$password}@{$host}"); 		
		$m = new Mongo();
		$db = $m->pois;
		$pois = $db->users;
		
		$condition = array('loc' => array('$near' => array(floatval($lng), floatval($lat))));
		if($maxDistance != 0) {
			$max_distance = floatval($maxDistance);
			$condition['loc']['$maxDistance'] = $max_distance/self::KM_PER_DEGREE;			
		}
		
		$skip = ($page-1)*$pageLimit;
		if($skip<0) $skip = 0;		
		
		$cursor = $pois->find($condition)->skip($skip)->limit($pageLimit);
		
		$data = iterator_to_array($cursor, false);
		foreach ($data as &$value) {			
			$distance = self::calcDistance($lat, $lng, $value['loc'][1], $value['loc'][0]);
			$value['distance'] = $distance;
		}
		return $data;
	}
}
