<?php
/*
*mygo全局函数
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
//加载文件
function LOAD($file){
	if(file_exists($file)){
    	include_once($file);
   	}else{
    	throw new Exception($file." is not exist");
    }
}
//创建model对象方法
function M($modelName){
	if(!$modelName) return null;
	$modelClass  = $modelName.'Model';
	return new $modelClass;
}

//调用widget挂件
function W($widgetName,$params){
	if($widgetName) return null;
	$widgetClass = $widgetName.'Widget';
	$widget = new $widgetClass($params);
	$widget->init();
}
?>