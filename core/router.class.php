<?php
/*
*mygo路由类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class MygoRouter {

	public function __construct(){

	}

	public  $defaultAutoRouter = array(
		'module'=>'home',
		'controller'=>'index',
		'action'=>'index',
	);
	public  $enableAutoMatch = true;
	
 	public  function autoMatch($requestPath){
 		$dispatchInfo = $this->defaultAutoRouter;
 		$patchArr = explode('/', $requestPath);
 		if($module=current($patchArr)){
 			$dispatchInfo['module'] = $module;
 		}
 		if($controller = next($patchArr)){
 			$dispatchInfo['controller'] = $controller;
 		}
 		if($action = next($patchArr)){
 			$dispatchInfo['action'] = $action;
 		}

 		$params = array();
 		while (false!==($next = next($patchArr))) {
 			$params[$next] = urldecode(next($patchArr));
 		}
 		Request::setParams($params);
 		return $dispatchInfo;
 	}

 	public function match($rules=null){
 		$requestPath = Request::getRequestPath();
 		if($rules){
	 		foreach ($rules as $regex => $rule) {
	 			if(!preg_match($regex, $requestPath,$matches)){
	 				continue;
	 			}
	 			if(isset($rule['maps']) && is_array($rule['maps'])){
	 				$params = array();
	 				foreach ($rule['maps'] as $key => $val) {
	 					if(isset($matches[$key]) && '' !=$matches[$key]){
	 						$params[$val] = urldecode($matches[$key]);
	 					}
	 					if(isset($rule['defaults'])){
	 						$params += $rule['defaults'];
	 					}
	 				}
	 				Request::setParams($params);
	 			}
	 			if(isset($rule['module'])){
	 				$dispatchInfo['module'] = $rule['module'];
	 			}
	 			if(isset($rule['controller'])){
	 				$dispatchInfo['controller'] = $rule['controller'];
	 			}
	 			if(isset($rule['action'])) {
	 				$dispatchInfo['action'] = $rule['action'];
	 			}
	 			return $dispatchInfo;
	 		}
 		}

 		if($this->enableAutoMatch){
 			return $this->autoMatch($requestPath);
 		}
 	}
}

?>