<?php
/*
*view 层代码
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class MygoView{

	protected $viewHome = '';
	protected $tplExt = ".tpl.html";

	public function __construct($viewHome){
		if(is_null($viewHome)){
			$this->viewHome=PRJDIR.'/modules/'.mygo::$dispatchInfo['module']."/view";
		}else{
			$this->viewHome= $viewHome;
		}
	}

	public function  display($tpl,$dir=''){
		ob_start();
        ob_implicit_flush(0);
        $this->fetch($tpl, $dir);
        $content = ob_get_contents();
        ob_get_clean();
        echo $content;
	}

	public function fetch($tpl,$dir=''){
		if (is_null($dir)) {
            $dir = $this->viewHome;
        }
        if(is_null($tpl)){
			$tpl = $this->defaultTemplate();
		}
		$tpl .=$this->tplExt; 
		$dir = rtrim($dir,"/\\").DIRECTORY_SEPARATOR;
        include ($dir . $tpl);
	}

	public function defaultTemplate(){
		$default = mygo::$controller.'/'.mygo::$action;
		return $default;
	}

	public function slice($tpl,array $data = array()){
		ob_start();
		$this->fetch($tpl);
		$content = ob_get_contents();
		ob_get_clean();
		return $content;
	}
}
?>