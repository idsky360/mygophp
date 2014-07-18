<?php
/*
*mygo核心类
*@author idsky<idsky360@163.com>
**/

defined('MYGODIR') || define('MYGODIR', dirname(__FILE__));
$auloadPath = MYGODIR.DIRECTORY_SEPARATOR.core.DIRECTORY_SEPARATOR.'autoload.class.php';
$globalFuncPath = MYGODIR.DIRECTORY_SEPARATOR.core.DIRECTORY_SEPARATOR.'global.func.php';
include_once($auloadPath);
include_once($globalFuncPath);

class mygo{

	private static $_instance;
	private static $routeConfigKey='route';

	public function __construct(){
		mygoAutoload::register();	
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
		//别名
		class_alias(mygoModel,model);
		class_alias(mygoController,controller);
		class_alias(mygoWidget,widget);
		
		//配置管理类
		class_alias(mygoConfig,C);
		//助手类
		class_alias(mygoExtHelper,H);

	}

	public static function run(){
		self::init();
		$rules = mygoConfig::getByName(self::$routeConfigKey);
		$router = new mygoRouter();
		$dispatchInfo = $router->match($rules);
		
		if($dispatchInfo['controller']){
			$className = $dispatchInfo['controller'];
			$controller = new $className();
		}
		if($dispatchInfo['action']){
			$action = $dispatchInfo['action'];
		}
		if($controller && $action){
			try {
				$func = array($controller,$action);
				call_user_func($func);	
			} catch (Exception $e) {
				print_r($e->getmessage());
				exit;
			}
		}else{
			throw new Exception('controller or action is not exit');
		}
	}

	public static function getDispatchInfo(){
		$rules = mygoConfig::getByName(self::$routeConfigKey);
		$router = new mygoRouter();
		$dispatchInfo = $router->match($rules);
		return $dispatchInfo;
	}
}

?>