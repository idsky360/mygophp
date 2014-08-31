<?php
/*
*mygo配置文件管理类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
**/
class Config {

	public static function getRunEnv(){
		if($_SERVER['RUN_ENV']){
			define('RUN_ENV',$_SERVER['RUN_ENV']);
		}else{
			include_once('/etc/env.php');
		}
		return defined(RUN_ENV) ? RUN_ENV : 'dev';
	}

	public static function getByName($name){
		global $MYGODATA;
		if(isset($MYGODATA['config'][$name])){
			return self::loadConfig($MYGODATA['config'][$name]);
		}
		$runEnv = self::getRunEnv();
		$configPath = PRJDIR."/config/".$name.".conf.php";		
		if(!file_exists($configPath)){
			$configPath = PRJDIR."/config/".$runEnv.'/'.str_replace('.','/',$name).".conf.php";		
		}
		if(file_exists($configPath)){
			$config = self::loadConfig($configPath);	
		}
		return $config;
	}

	//$key  文件名.配置项  例  'common.mysql'
	public static function getByKey($key,$default=null){
		$config = array();
		$_array = explode('.',$key);
		$_key = array_pop($_array);
		$_name = implode('/',$_array);
		$allConfig = self::getByName($_name);
		if($allConfig && $allConfig[$_key]){
			$config = $allConfig[$_key];
		}
		return $config ? $config : $default;
	}

	public static function loadConfig($configPath){
		$config = include($configPath);
		return $config;
	}
}

