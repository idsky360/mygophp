<?php
/*
*mygo 助手类
**/
class mygoExtHelper{
	
	//对象转数组
	public static function objToArr($obj){
	    if(is_object($obj)) {
	        $obj = (array)$obj;
	        $obj = self::objToArr($obj);
	    } elseif(is_array($obj)) {
	        foreach($obj as $key => $value) {
	            $obj[$key] = self::objToArr($value);
	        }
	    }
	    return $obj;
	}

	//数组转对象
	public static function arrToObj($arr){
		if(is_array($arr)){
			$arr = (object) $arr;
			$arr = self::arrToObj($arr);
		}elseif(is_object($arr)){
			foreach($arr as $key=>$value){
				$arr->$key = self::arrToObj($value);
			}
		}
		return $arr;
	}
}
?>