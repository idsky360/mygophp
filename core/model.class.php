<?php
/*
*model 基类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
abstract class mygoModel {
	//mysql 对象
	private $mysql;
	private $redis;
	
	public function __construct(){

	}

	public function mysql($config=null){
		return $this->mysql = mygoExtMysqlDriver::getInstance($config);
	}

	public function redis($config=null){
		return $this->redis = new mygoExtRedisDriver($config);
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