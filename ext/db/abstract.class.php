<?php
include_once('sqlBuilder.class.php');

abstract class DbAbstract{

	//单例模式实现对象
    protected static $_instance;

	//数据库配置文件
	protected $config;

	//当前数据库配置
    protected $_dbConfig;

	//数据库链接 支持多个
	protected $dbLinks = array();

    //当前数据库链接 
    protected $dbLink;

    //查询的数据库
    protected $database;

    //是否主从模式
    protected $isMasterSlave = false;

    //执行结果
    protected $execResult;

    //sql error
    protected $sqlError;

    //最近一次执行的sql语句
    public $sql;

    //是否开启事务
    private $transaction = false;

    //sql构造器
    public $sqlBuilder;

    public function __construct($config){
    	$this->config = $config;
    	if(isset($config['master']) && isset($config['slave'])){
    		$this->isMasterSlave = true;
    	}
    }

    public static function getInstance($config=''){
        if (!self::$_instance) {
            $className = get_called_class();
            self::$_instance = new $className($config);
        }
        return self::$_instance;
    }

    protected function initConnect(){
    	if(!$this->database){
    		$this->database = current(array_keys($this->config));
    	}
    	if($this->isMasterSlave){
    		$_type = $this->isMorS($this->sql);
    		$this->_dbConfig = $this->config[$this->database][$_type];	
    	}else{
    		$_type = 'single';
    		$this->_dbConfig = $this->config[$this->database];
    	}
        //参数判断
        if(!$this->_dbConfig['host'] || !$this->_dbConfig['username'] || !$this->_dbConfig['password']){
            throw new Exception('mysql config host or username or password is null');
        }
    	if(!$tihs->dbLinks[$_type]){
            $this->_dbConfig['port'] = $this->_dbConfig['port'] ? $this->_dbConfig['port'] : '3306';
    		$this->dbLinks[$_type] = $this->connect();
    	}
    	$this->dbLink = $this->dbLinks[$_type];
    	$this->selectDb();
    }

    private function isMorS($sql){
        $is = 'slave';
        if($this->transaction){
            $is = 'master';
        }else{
            $_mkws = array('insert','replace','update','delete');
            $_arr = explode(' ',trim($sql),2);
            if(in_array(strtolower($_arr[0],$_mkws))){
                $is= 'master';
            }            
        }
        return $is;
    }

    abstract protected function connect();
    abstract protected function selectDb();
    abstract public function exec($sql,$database='');
    abstract public function fetchArray($key);
    abstract public function fetchObject($key);
    abstract public function fetchRow();
    abstract public function fetchFirstColumn();
    abstract public function begin();
    abstract public function commit();
    abstract public function rollback();
    abstract public function getLastInsertId();
    abstract public function getRowsNum();
    abstract protected function error();  
    /****************************以下方法利用sql生成器生成sql*********************************************/

    public function insert($data,$options,$replace=false){
        if($replace){
            $sql = $this->sqlBuilder->buildReplaceSql($data,$options);
        }else{
            $sql = $this->sqlBuilder->buildInsertSql($data,$options);
        }
        return $this->exec($sql,$options['database'])->getLastInsertId();
    }

    public function delete($where,$options){
        $sql = $this->sqlBuilder->buildDeleteSql($where,$options);
        return $this->exec($sql,$options['database'])->getNumRows();
    }

    public function update($data,$where,$options){
        $sql = $this->sqlBuilder->buildUpdateSql($data,$where,$options);
        return $this->exec($sql,$options['database'])->getNumRows();
    }

    //查询数据
    public function select($fields,$where,$options){
        $sql = $this->sqlBuilder->buildSelectSql($fields,$where,$options);
        return $this->dosql($sql,$options['database'])->fetchArray();
    }

    //查询一条数据
    public function selectOne($fields,$where,$options){
        $sql = $this->sqlBuilder->buildSelectSql($fields,$where,$options);
        return $this->dosql($sql,$options['database'])->fetchRow();
    }
    
    //count
    public function count($where,$options){
        $sql = $this->sqlBuilder->buildCountSql($where,$options);
        return $this->query($sql,$options['database'])->fetchFirstColumn();
    }

 }

