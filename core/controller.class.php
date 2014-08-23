<?php
/*
*controller 基类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
**/
abstract class MygoController {
	protected $title;
	protected $keywords;
	protected $description;
	private $model;
	private $view;
	private $config;
	private $request;
	private $response;

	public function __construct(){

	}

	protected function get($key,$default=''){
		return Request::get($key,$default);
	}

	protected function post($key,$default=''){
		return Request::post($key,$default);
	}

	protected function input($parse=false){
		return Request::input($parse);
	}

    protected function redirect($url, $code = 302){
        Response::redirect($url,$code);
    }

    protected function forward($controller,$action='index'){
    	$className = $controller."Controller";
    	$action = $action."Action";
    	$controller = new $className();
    	if($controller && $action){
    		$func = array($controller,$action);
			call_user_func($func);
    	} 
    }

	protected function model($model=null){
		if(!$model){
			$controllerClass = get_class($this);
			$modelClass = str_replace('Controller','Model',$controllerClass);
		}else{
			$modelClass = $model.'Model';	
		}
		return new $modelClass;
	}

	protected function view($viewsHome=null){
		return $this->view = new mygoView($viewsHome);
	}

	protected function display($tpl=null,$dir=null){
		return $this->view->display($tpl,$dir);
	}

	protected function slice($tpl,array $data=array()){
		return $this->view->slice($tpl,$data);
	}

	protected function json($status=1,$info='',$data='',$header=true){
		return Response::json($status,$info,$data,$header);
	}
	
	protected function xml($xml,$header=true){
		return Response::xml($xml,$header);
	}

    public function __set($key, $value = null){
        $this->$key = $value;
    }

	public function __get($key){
		switch ($key) {
			case 'model':
				return $this->model ? $this->model : $this->model = $this->model();
				break;
			case 'view':
				return $this->view ?  $this->view : $this->view = $this->view();
				break;
			case 'config':
				return $this->config ? $this->config : $this->config = new C();
				break;
			case 'request':
				return $this->request ? $this->request : $this->request = new Request();
				break;
			case 'response':
				return $this->response ? $this->response : $this->response = new Response();
				break;
			default:
				break;
		}
	}
}

?>