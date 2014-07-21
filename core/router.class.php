<?php
/*
*mygo路由类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class mygoRouter {

	public function __construct(){

	}

	public  $defaultAutoRouter = array(
		'controller'=>'indexController',
		'action'=>'indexAction',
	);
	public  $enableAutoMatch = true;
	
 	public  function autoMatch($requestPath){
 		$dispathInfo = $this->defaultAutoRouter;
 		$patchArr = explode('/', $requestPath);
 		if($controller=current($patchArr)){
 			$dispathInfo['controller'] = $controller;
 		}
 		if($action=next($patchArr)){
 			$dispathInfo['action'] = $action;
 		}

 		$params = array();
 		while (false!==($next = next($patchArr))) {
 			$params[$next] = urldecode(next($patchArr));
 		}
 		mygoRequest::setParams($params);
 		return $dispathInfo;
 	}

 	public function match($rules=null){
 		$requestPath = mygoRequest::getRequestPath();
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
	 				mygoRequest::setParams($params);
	 			}
	 			if(isset($rule['controller'])) $rule['controller'] = $rule['controller'].'Controller';
	 			if(isset($rule['action'])) $rule['action'] = $rule['action'].'Action';
	 			return $rule;
	 		}
 		}

 		if($this->enableAutoMatch){
 			return $this->autoMatch($requestPath);
 		}
 	}
}

?>