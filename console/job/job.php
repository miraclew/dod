<?php
abstract class Job {
	public abstract function perform();
	
	/**
	 * 校验需存在字段
	 * @param array $data
	 * @param array $fileds
	 * @return array 第一元素为是否成功，第二个为缺失字段(如果有)
	 */
	protected function validatePresence($data, $fields)
	{
		foreach ($fields as $f) {
			if(!isset($data[$f]))
				return array(false, $f);
		}
		return array(true);
	}
	
}