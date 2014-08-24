<?php
/*
*model 基类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
abstract class MygoModel {
	//mysql 对象
	private $mysql;
	private $redis;
	
	public function __construct(){

	}

	public function mysql(){
		$mysqlConfig = C::getByname('mysql');
		return $this->mysql = DbMysqlDriver::getInstance($mysqlConfig);
	}

	public function redis($config=null){
		$redisConfig = C::getByname('redis');
		return $this->redis = new CacheRedisDriver($redisConfig);
	}

	public function __set($key, $value = null){
        $this->$key = $value;
    }

	public function __get($key){
		switch ($key) {
			case 'mysql':
				return $this->mysql ? $this->mysql : $this->mysql();
				break;
			case 'redis':
				return $this->redis ? $this->redis : $this->redis();
				break;
			default:
				break;
		}
	}
}

?>