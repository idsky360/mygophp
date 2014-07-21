<?php
/*
*mygo 自动加载类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class mygoAutoload{

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
		
		if(substr($className,0,4)==='mygo'){
			if(substr($className,0,7)==='mygoExt'){
				//加载框架扩展类
				$classFile = MYGODIR.DIRECTORY_SEPARATOR.'ext'.DIRECTORY_SEPARATOR.
				strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/',DIRECTORY_SEPARATOR, str_replace('mygoExt','',$className))).".class.php";
			}else{
				//加载框架类
				$classFile = MYGODIR.DIRECTORY_SEPARATOR.'core'.DIRECTORY_SEPARATOR.strtolower(str_replace('mygo','',$className)).'.class.php';
			}
		}else if(substr($className,-10)==='Controller'){
			//加载Controller类
			$classFile = PRJDIR.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.str_replace('Controller','',$className).'.class.php';
		}else if(substr($className,-5)==='Model'){
			//加载Model类
			$classFile = PRJDIR.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.str_replace('Model','',$className).'.class.php';
		}else if(substr($className,-6)=='Widget'){
			//加载Widget类
			$classFile = PRJDIR.DIRECTORY_SEPARATOR.'widget'.DIRECTORY_SEPARATOR.str_replace('Widget','',$className).'.class.php';
		}else{
			//按规则自动加载
			$classFile = PRJDIR.DIRECTORY_SEPARATOR.strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/',DIRECTORY_SEPARATOR, $className)).".class.php";			
			
			if(!file_exists($classFile)){
				//尝试在lib包和自定义的目录中查找类
				$autoloadPath = array(
					//MYGODIR.DIRECTORY_SEPARATOR.'ext',
					//PRJDIR.DIRECTORY_SEPARATOR.'ext',
					MYGODIR.DIRECTORY_SEPARATOR.'lib',
					PRJDIR.DIRECTORY_SEPARATOR.'lib',
				);
				$autoloadConfig = mygoConfig::getByName('autoload');
				if($autoloadConfig && is_array($autoloadConfig)){
					$autoloadPath = array_merge($autoloadPath,$autoloadConfig);
				}
				foreach ($$autoloadPath as $path){
					//TODO 缓存
					$classFile = rtrim($path,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$className.'.class.php';
					if(file_exists($classFile)) break;
				}		
			}
		}
		if(file_exists($classFile)) {
            include_once($classFile);
        }else{
			throw new Exception($classFile." is not exist");
		}
        return (class_exists($className, false) || interface_exists($className, false));
	}

	public static function register($func = 'self::autoload', $enable = true)
    {
        return $enable ? spl_autoload_register($func) : spl_autoload_unregister($func);
    }
}
?>