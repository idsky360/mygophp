<?php
/*
*mygo 助手类
**/
class mygoExtHelper{

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

	public static function xmlToObj($xmlString){
		return simplexml_load_string($xmlString);
	}
	
	public static function xmlToArr($xml){
		return self::objToArr(simplexml_load_string($xmlString));
	}
}
?>