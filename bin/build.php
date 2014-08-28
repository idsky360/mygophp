<?php
/*
*自动创建项目
*/
include_once(realpath(dirname(__FILE__)."/../mygo.class.php"));
//初始化
Mygo::init();
define(ROOTDIR,MYGODIR.'/../');
$paramArr = getopt('P:M:');
$projectName = $paramArr['P'];
$module = $paramArr['M'];

$projectDir = MYGODIR.'/../'.$paramArr['P'];
define('PROJDIR',$projectDir);
if(!$module && file_exists(PROJDIR)){
	echo "项目".PROJDIR."已存在!\n";
	exit;
}

buildProjectDir();
buildConfig();
buildIndex($projectName);
buildModule($module);

function buildProjectDir(){
	if(is_writeable(ROOTDIR)){
		$dirs = array(
			PROJDIR,
			PROJDIR.'/webroot',
			PROJDIR.'/config',
			PROJDIR.'/modules',
			//PROJDIR.'/widget',
			PROJDIR.'/lib', //项目类包
			PROJDIR.'/vendor',//第三方包
		);
	    foreach ($dirs as $dir){
	        if(!is_dir($dir) && !file_exists($dir))  mkdir($dir,0755,true);
	    }		
	}else{
		echo "目录".ROOTDIR."不可写,无法自动生成,请手动创建项目目录～";
        exit;
	}
}

function buildIndex($projectName){
	$indexFile = PROJDIR.'/webroot/index.php';
$content = '<?php
//定义项目目录
define(PRJDIR,realpath(dirname(__FILE__)."/../../'.$projectName.'"));
include_once(PRJDIR."/../mygophp/mygo.class.php");	
mygo::run();';
	if(!file_exists($indexFile)){
		file_put_contents($indexFile,$content);
	}
}

function buildConfig(){
	$dirs = array(
		PROJDIR.'/config/dev',
		PROJDIR.'/config/qa',
		PROJDIR.'/config/pro',
	);
	foreach ($dirs as $dir){
	    if(!is_dir($dir) && !file_exists($dir))  mkdir($dir,0755,true);
	}
	_buildRouterConfig();
	_buildAutoloadConfig();
	_buildCommonConfig();
	_buildMysqlConfig();
	_buildRedisConfig();
}

function _buildCommonConfig(){
$devContents = '<?php
return array(
	"debug"=>true,
);
';
$qaContents = '<?php
return array(
	"debug"=>true,
);
';
$proContents = '<?php
return array(
	"debug"=>false,
	"db_adapter"=>"pdo",//数据库适配 mysql mysqli pdo
);
';
	$commonFiles = array(
		PROJDIR.'/config/dev/common.conf.php'=>$devContents,
		PROJDIR.'/config/qa/common.conf.php'=>$qaContents,
		PROJDIR.'/config/pro/common.conf.php'=>$proContents,
	);
	foreach ($commonFiles as $file => $content) {
		if(!file_exists($file)){
			file_put_contents($file,$content);
		}
	}
}

function _buildRouterConfig(){
$content = '<?php
return array(
	"|^home\/index\/index\/(\d+)|"=>array(
		"module"=>"home",
		"controller"=>"index",
		"action"=>"index",
		"maps"=>array("1"=>"type"),
	),
);
';
	$routerFile = PROJDIR.'/config/route.conf.php';
	if(!file_exists($routerFile)){
		file_put_contents($routerFile,$content);
	}
}

function _buildAutoloadConfig(){
$content = '<?php
return array(
	"vendor",
	"lib",
);
';
	$autoloadFile = PROJDIR.'/config/autoload.conf.php';
	if(!file_exists($autoloadFile)){
		file_put_contents($autoloadFile,$content);
	}
}

function _buildMysqlConfig(){
$content = '<?php
return array(
	//数据库 demo
	//单服务器
	"demo" => array(
		"host"=>"127.0.0.1",
		"username"=>"root",
		"password"=>"123456",
		"port"=>"3306",
		"charset"=>"utf8",
	),

	//主从
	/*
	"demo" => array(
		"master"=>array(
			"host"=>"127.0.0.1",
			"username"=>"root",
			"password"=>"123456",
			"port"=>"3306",
			"charset"=>"utf8",
		),
		"slave"=>array(
			"host"=>"127.0.0.1",
			"username"=>"root",
			"password"=>"123456",
			"database"=>"demo",
			"charset"=>"utf8",
		),

	),
	*/
	//一主多从
	/*
	"demo" => array(
		"master"=>array(
			"host"=>"127.0.0.1",
			"username"=>"root",
			"password"=>"123456",
			"port"=>"3306",
			"charset"=>"utf8",
		),
		"slave"=>array(
			array(
				"host"=>"127.0.0.1",
				"username"=>"root",
				"password"=>"123456",
				"port"=>"3306",
				"charset"=>"utf8",
			),
			array(
				"host"=>"127.0.0.1",
				"username"=>"root",
				"password"=>"123456",
				"port"=>"3306",
				"charset"=>"utf8",
			),
		),
	),
	*/
);';
	$mysqlFiles = array(
		PROJDIR.'/config/dev/mysql.conf.php'=>$content,
		PROJDIR.'/config/qa/mysql.conf.php'=>$content,
		PROJDIR.'/config/pro/mysql.conf.php'=>$content,
	);
	foreach ($mysqlFiles as $file => $content) {
		if(!file_exists($file)){
			file_put_contents($file,$content);
		}
	}
}

function _buildRedisConfig(){
$content = '<?php
return array(
	"host"=>"127.0.0.1",
	"port"=>"",
);
';
	$redisFiles = array(
		PROJDIR.'/config/dev/redis.conf.php'=>$content,
		PROJDIR.'/config/qa/redis.conf.php'=>$content,
		PROJDIR.'/config/pro/redis.conf.php'=>$content,
	);
	foreach ($redisFiles as $file => $content) {
		if(!file_exists($file)){
			file_put_contents($file,$content);
		}
	}
}

function buildModule($module){
	$defaultDispatchInfo = Mygo::getDispatchInfo();
	$module = $module ? $module : $defaultDispatchInfo['module'];
	$controller = $defaultDispatchInfo['controller'];
	$action = $defaultDispatchInfo['action'];
	$dirs = array(
		PROJDIR.'/modules/'.$module,
		PROJDIR.'/modules/'.$module.'/model',
		PROJDIR.'/modules/'.$module.'/controller',
		PROJDIR.'/modules/'.$module.'/view',
	);
	foreach ($dirs as $dir){
	    if(!is_dir($dir) && !file_exists($dir))  mkdir($dir,0755,true);
	}
	_buildDefaultModel($module);
	_buildDefaultController($module);
	_buildDefaultView($module);
}

function _buildDefaultModel($module){
	$modelFile = PROJDIR.'/modules/'.$module.'/model/index.class.php';
$content = '<?php
class '.ucfirst($module).'IndexModel extends model{
	public function demo(){
		//$sql = "select * from table where 1";
		//$result = $this->mysql->query($sql)->fetchRows();
		$result  = array("msg"=>"this is mygophp demo");
		return $result;
	}
}';
	if(!file_exists($modelFile)){
		file_put_contents($modelFile,$content);
	}
}

function _buildDefaultController($module){
	$controllerFile = PROJDIR.'/modules/'.$module.'/controller/index.class.php';
$content = '<?php
class '.ucfirst($module).'IndexController extends controller{
	public function indexAction(){
		//$data = M("HomeIndex")->demo();
		$data = $this->model->demo();
		$this->view->data=$data;
		$this->title = "mygophp demo";
		$this->keywords = "mygophp demo";
		$this->description = "mygophp demo";
		$this->display();
	}
}';
	if(!file_exists($controllerFile)){
		file_put_contents($controllerFile,$content);
	}
}

function _buildDefaultView($module){
	$viewDir = PROJDIR.'/modules/'.$module.'/view/index';
	if(!is_dir($viewDir) && !file_exists($viewDir))  mkdir($viewDir,0755,true);
	$viewFile = $viewDir.'/index.tpl.html';
$content = '<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<title><?php echo $this->title;?></title>
		<meta name="keywords" content="<?php echo $this->keywords;?>">
		<meta name="description" content="<?php echo $this->description;?>">
	</head>
	<body>
		<h2><?php echo $this->data["msg"];?></h2>
	</body>
</html>';
	if(!file_exists($viewFile)){
		file_put_contents($viewFile,$content);
	}
}

