<?php
/*
*redis 驱动类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
**/
class CacheRedisDriver{

	protected $config;

	protected $redis;

	public function __construct($config=null){
		$this->config = $config;
		$this->connect();
	}

	protected function connect(){
		if(!$this->config['host']){
			throw new Exception('redis host is null');
		}
		$this->config['port'] = $this->config['port'] ? $this->config['port'] : '6379';
		$this->config['timeout'] = $this->config['timeout'] ? $this->config['timeout'] : 0;
		$this->config['persistent'] = $this->config['persistent'] ? $this->config['persistent'] : false;
		$func = $options['persistent'] ? 'pconnect' : 'connect';
		$this->redis = new Redis(); 
		$this->config['timeout'] ? 
			$this->redis->$func($this->config['host'],$this->config['port']) : 
			$this->redis->$func($this->config['host'],$this->config['port'],$this->config['timeout']);
	}

	//获取key的类型 
	public function type($key){
		return $this->redis->type($key);
	}
	
	//string类型函数

	public function set($key,$value,$expire=86400){
		$res = $this->redis->set($key,$value);
		if($expire){
			$this->expire($key,$expire);
		}
		return $res;
	}

	public function get($key){
		return $this->redis->get($key);
	}

	//自增 必须为数字类型 默认增加1
	public function incr($key,$increment=null){
		if(!$increment){
			return $this->redis->incr($key);
		}
		if(is_int($increment)){
			return $this->redis->incrBy($key,$increment);
		}
		if(is_float($increment)){
			return $this->redis->incrByFloat($key,$increment);
		}
		return false;
	}

	//自减 
	public function decr($key,$decrement=null){
		if(!$decrement){
			return $this->redis->decr($key);
		}
		if(is_int($decrement)){
			return $this->redis->decrBy($key,$decrement);
		}
		if(is_float($decrement)){
			return $this->redis->decrByFloat($key,$decrement);
		}
		return false;
	}

	//一次设置多个key value  $data=array('key'=>'value');
	public function mset(array $data,$expire=86400){
		$res = $this->redis->mSet($data);
		if($expire){
			foreach($data as $k => $val){
				$this->expire($k,$expire);
			}
		}
		return $res;
	}

	//一次获取多个key的值 $keys = array('key1','key2',...);
	public function mget(array $keys){
		return $this->redis->mGet($keys);
	}

	//设置key多长时间后过期
	public function expire($key,$expire){
		return $this->redis->expire($key,$expire);
	}

	//设置key的过期时间  
	public function expireAt($time){
		return $this->redis->expireAt($time);
	}

	//hash类型函数
	public function hset($key,$field,$value,$expire=86400){
		$res = $this->redis->hSet($key,$field,$value);
		if($expire){
			$this->expire($key,$expire);
		}
		return $res;
	}

	public function hget($key,$field){
		return $this->redis->hGet($key,$field);
	}

	public function hmset($key,array $data,$expire=86400){
		$res = $this->redis->hMset($key);
		if($expire){
			$this->expire($key,$expire);
		}
	}

	public function hmget($key,array $fields){
		return $this->redis->hMget($key,$fields);
	}

	public function hgetAll($key){
		return $this->redis->hGetAll($key);
	}

	public function hincr($key,$field,$increment=1){
		if(is_numeric($increment)){
			return $this->redis->hIncrBy($key,$field,$increment);
		}
		if(is_float($increment)){
			return $this->redis->hIncrByFloat($key,$field,$increment);
		}
		return false;
	}

	public function hdel($key,$field){
		return $this->redis->hDel($key,$field);
	}

	//list

	//set 

	//删除key  支持多个
	public function delete($keys){
		return $this->redis->delete($keys);
	}
}
?>