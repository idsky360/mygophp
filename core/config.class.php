<?php
/*
*mygo配置文件管理类
*@author idsky<idsky360@163.com>
**/
class mygoConfig {

	public static function getRunEnv(){
		include_once('/etc/env.php');
		return defined(RUNENV) ? RUNENV : 'development';
	}

	public static function getByName($name){
		global $MYGODATA;
		if(isset($MYGODATA['config'][$name])){
			return self::loadConfig($MYGODATA['config'][$name]);
		}
		$runEnv = self::getRunEnv();
		$configPath = PRJDIR.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$name.".conf.php";		
		if(!file_exists($configPath)){
			$configPath = PRJDIR.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$name.".".$runEnv.".conf.php";		
		}
		$config = self::loadConfig($configPath);
		return $config;
	}

	//$key  文件名.配置项  例  'common.mysql'
	public static function getByKey($key){
		$config = array();
		$_array = explode('.',$key);
		$_name = $_array[0];
		$_key = $_array[1];
		$allConfig = self::getByName($_name);
		if($allConfig && $allConfig[$_key]){
			$config = $allConfig[$_key];
		}
		return $config;
	}

	public static function loadConfig($configPath){
		$config = include($configPath);
		return $config;
	}
}

