<?php
/*
*@response 相关类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class mygoResponse{

	public static function redirect($url, $code = 302)
    {
        header("Location:$url", true, $code);
        exit();
    }

    public static function json($status=1,$info='',$data='',$header=true){
    	$jsonData = array();
    	$jsonData['status'] = $status;
    	$jsonData['info'] = $info;
    	$jsonData['data'] = $data;
    	if($header){
           header("Content-Type:application/json;charset=UTF-8");
        }
        ob_clean();//clear output:Notice and others
        echo json_encode($jsonData);
        exit();
    }
    public static function xml($xml,$header=true){
        if($header){
            header("Content-Type:application/xml;charset=UTF-8");
        }
        ob_clean();
        echo $xml;
        exit();
    }
}
?>