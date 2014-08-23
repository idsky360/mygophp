<?php
/*
*挂件 基类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
**/
abstract class MygoWidget extends mygoController{
	
	protected $data;

	public function __construct($params){
		parent::__construct();
		$this->data = $params;
	}

	public function display($tpl=null,$dir=null){
		if(!$dir){
			$dir = PRJDIR.DIRECTORY_SEPARATOR."view".DIRECTORY_SEPARATOR."widget";
		}
		if(!$tpl){
			$tpl = substr(get_class(),-6);
		}
		return $this->view->display($tpl,$dir);
	}
	
	//抽象方法 子类中必须重写这个方法
	abstract function init();
}
?>