<?php
/*
*request 相关类
@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class mygoRequest{
	
	// 获取uri
	public static function getRequestUri(){
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
	}

	//获取path
	public static function getRequestPath(){
		$uri = self::getRequestUri();
		$path ='';
		if($uri){
			$uri = substr($uri, 1);
			$path = substr($uri,0,strpos($uri,"?"));
		}
		return $path;
	}

	//设置请求参数
	public static function setParams($params){
		if($params && is_array($params)){
			foreach ($params as $key => $value){
				if($key && $value){
					$_GET[$key] = $value;
				}
			}
		}
		return;
	}

	//获取get参数
	public static function get($key,$default=''){
		if(null===$key){
			return $_GET;
		}
		return $_GET[$key] ? $_GET[$key] : $default;
	}

	//获取post参数
	public static function post($key,$default=''){
		if(null===$key){
			return $_POST;
		}
		return $_POST[$key] ? $_POST[$key] : $default;
	}

	//获取input数据
	public static function input(){
		parse_str(file_get_contents('php://input'), $input);
		return $input;
	}

	//获取所有请求参数
	public static function getAllParams(){
		$params = array();
		if($_GET) $params += $_GET;
		if($_POst) $params +=$_POST;
		return $params;
	}
}
?>