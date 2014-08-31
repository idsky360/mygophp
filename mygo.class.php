<?php
/*
*mygo核心类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
**/

defined('MYGODIR') || define('MYGODIR', dirname(__FILE__));
include_once (MYGODIR.'/lib/global.func.php');
class Mygo{

	public static $dispatchInfo;
	private static $_instance;
	public function __construct(){
		self::importCore();
		MygoAutoload::register();	
	}

	public static function getInstance(){
		if(null===self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	//初始化项目
	public static function init(){
		global $MYGODATA;
		if(!self::$_instance){
			self::getInstance();
		}
		//设置别名
		self::alias();
	}

	public static function run(){
		self::init();
		self::getdispatchInfo();
		if(self::$dispatchInfo['module'] && self::$dispatchInfo['controller']){
			$className = ucfirst(self::$dispatchInfo['module']).ucfirst(self::$dispatchInfo['controller']).'Controller';
			$object = new $className();
		}
		if(self::$dispatchInfo['action']){
			$func = self::$dispatchInfo['action'].'Action';
		}

		if($object && $func){
			try {
				call_user_func(array($object,$func));
			} catch (Exception $e) {
				$error = $e->getFile()." on line ".$e->getLine()." ".$e->getMessage()." ";
				$error .= $e->getTraceAsString();
				$debug = C::getByKey("common.debug",false);
				if($debug){
					print_r($error);	
				}else{
					Log::record($error,"error");
					header("Content-type: text/html; charset=utf-8",false,500);
				}
				exit;
			}
		}else{
			//TODO 404
		}
	}

	//get dispatchInfo
	public static function getdispatchInfo(){
		$rules = C::getByName('route');
		$router = new mygoRouter();
		self::$dispatchInfo = $router->match($rules);
		return self::$dispatchInfo;
	}

	private static function alias(){
		class_alias(MygoModel,model);
		class_alias(MygoController,controller);
		class_alias(MygoWidget,widget);
		//配置管理类
		class_alias(Config,C);
		class_alias(Helper,H);
	}

	private static  function importCore(){
		LOAD(MYGODIR.'/lib/config.class.php');
		LOAD(MYGODIR.'/core/autoload.class.php');
		LOAD(MYGODIR.'/core/router.class.php');
		LOAD(MYGODIR.'/core/model.class.php');
		LOAD(MYGODIR.'/core/controller.class.php');
		LOAD(MYGODIR.'/core/view.class.php');
		LOAD(MYGODIR.'/core/widget.class.php');
	}

}
?>