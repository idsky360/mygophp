<?php
/*
*@response 相关类
*@author idsky<idsky360@163.com>
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
}
?>