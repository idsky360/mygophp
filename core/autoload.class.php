<?php
/*
*mygo 自动加载类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class MygoAutoload{

	public static function autoload($className,$classFile=''){
		if (class_exists($className, false) || interface_exists($className, false)) {
            return true;
        }
		//autoload class file
		if(isset($classFile) && file_exists($classFile)){
			include_once($classFile);
			unset($classFile);
			return (class_exists($className, false) || interface_exists($className, false));
		}
		
		//加载Controller类
		if(substr($className,-10)==='Controller'){
			$arr = preg_split("/(?=[A-Z])/",str_replace('Controller','',$className)); 
			$classFile = PRJDIR.'/modules/'.strtolower(current($arr)).'/controller/'.strtolower(end($arr)).'.class.php';
		//加载Model类
		}else if(substr($className,-5)==='Model'){
			$arr = preg_split("/(?=[A-Z])/",str_replace('Model','',$className)); 
			$classFile = PRJDIR.'/modules/'.strtolower(current($arr)).'/model/'.strtolower(end($arr)).'.class.php';
		//加载Widget类 TODO
		/*
		}else if(substr($className,-6)=='Widget'){
			$classFile = PRJDIR.DIRECTORY_SEPARATOR.'widget'.DIRECTORY_SEPARATOR.str_replace('Widget','',$className).'.class.php';
		*/
		}else{
			//按规则自动加载			
			if(!file_exists($classFile)){
				//自动尝试加载类
				$autoloadMap = array(
					MYGODIR.'/lib',
					MYGODIR.'/ext',
					PRJDIR.'/lib',
					PRJDIR.'/vendor',
				);
				$autoloadConfig = C::getByName('autoload');
				if($autoloadConfig && is_array($autoloadConfig)){
					foreach($autoloadConfig as &$val){
						$val = PRJDIR.'/'.rtrim($val,'/');
					}
					$autoloadMap = array_merge($autoloadMap,$autoloadConfig);
				}
				foreach ($autoloadMap as $path){
					$classFile = rtrim($path,'/').'/'.strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/',DIRECTORY_SEPARATOR, $className)).'.class.php';
					if(file_exists($classFile)) break;
				}		
			}
		}
		LOAD($classFile);
        return (class_exists($className, false) || interface_exists($className, false));
	}

	public static function register($func = 'self::autoload', $enable = true)
    {
        return $enable ? spl_autoload_register($func) : spl_autoload_unregister($func);
    }
}
?>