<?php
/*
*mygo全局函数
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/

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