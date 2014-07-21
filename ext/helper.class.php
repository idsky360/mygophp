<?php
/*
*mygo 助手类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
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

	//xml
	public static function parseXml($xmlStr,$attribute=false){
		$xmlObj = simplexml_load_string($xmlStr,'SimpleXMLElement',LIBXML_NOCDATA);
		$xmlArr = self::objToArr($xmlObj);
		foreach($xmlArr as $k=>$v){
			if(empty($v)){
				unset($xmlArr[$k]);
			}
		}
		$_attributes = array();
		if($attribute){
			foreach($xmlObj as $key=>$child){
				$attributeData = $child->attributes();
				$attributeArr = self::objToArr($attributeData);
				$_attributes[$key] = $attributeArr['@attributes'];
			}
			$xmlArr['_attributes'] = $_attributes;
		}	
		return $xmlArr;
	}
}
?>