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
		self::getDispatchInfo();
		if(self::$dispatchInfo['module'] && self::$dispatchInfo['controller']){
			$className = self::$dispatchInfo['module'].ucfirst(self::$dispatchInfo['controller']).'Controller';
			$object = new $className();
		}
		if(self::$dispatchInfo['action']){
			$func = self::$dispatchInfo['action'].'Action';
		}

		if($object && $func){
			try {
				call_user_func(array($object,$func));
			} catch (Exception $e) {
				print_r($e->getmessage());
				exit;
			}
		}else{
			throw new Exception('controller or action is not exit');
		}
	}

	//get dispatchInfo
	public static function getDispatchInfo(){
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
		//助手类
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