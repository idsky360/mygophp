<?php
/*
*数据库 mysql驱动类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
include_once('sqlBuilder.class.php');
class mygoExtMysqlDriver {

    //单例模式实现对象
    protected static $_instance;

	//数据库配置文件
	protected $config = array();

	//数据库链接
	protected $connections = array();

    //当前数据库链接 
    protected $dbLink;

    //是否主从模式
    protected $isMasterSlave = false;

    //查询结果
    protected $queryResult;

    //sql error
    protected $sqlError;

    //num rows
    protected $numRows;

    //last insert ID
    protected $lastInsertId;

    //sql构造器
    public $sqlBuilder;

    //最近一次执行的sql语句
    public $sql;

    //是否开启事务
    private $transaction = false;

    //构造函数
	public function __construct($config=''){
        $this->sqlBuilder = new sqlBuilder();
        if(!$config){
            $this->config = mygoConfig::getByKey('common.mysql');
            if($this->config['master'] && $this->config['slave']){
                $this->isMasterSlave = true;
            }
        }else{
            $this->config = $config;
            $this->initConnect();
        }
	}

	//单例模式
    public static function getInstance($config=''){
        if (!self::$_instance) {
            self::$_instance = new self($config);
        }
        return self::$_instance;
    }
    
    //创建数据库链接
    private function connect(array $config){
        //参数判断
        if(!$config['host'] || !$config['username'] || !$config['password']){
            throw new Exception('mysql config host or username or password is null');
        }
        $config['port'] = $config['port'] ? $config['port'] : '3306';
        $con = @mysql_connect($config['host'].':'.$config['port'],$config['username'],$config['password']);
        if(!$con){
            $this->error();
            return false;
        }
        return $con;
    }

    //初始化数据库链接
    private function initConnect($database='',$master=true){
        if($this->isMasterSlave){
            $_connectType = $master ? 'master' : 'slave';
            if($_connectType=='slave'){
                //多从
                $isMultiple ＝ 0；
                foreach($this->config[$_connectType] as $val){
                    if(is_array($val)){
                        $isMultiple ＝ 1；
                        break;
                    }
                    break;
                }
            }
            if($_connectType=='slave' && $isMultiple){
                $rand = rand(0,count($this->config[$_connectType])-1);
                $_dbConfig = $this->config[$_connectType][$rand];
            }else{
                $_dbConfig = $this->config[$_connectType];
            }
        }else{
            $_connectType = 'single';
            $_dbConfig = $this->config;
        }
        if(!$this->connections[$_connectType] && !mysql_ping($this->connections[$_connectType])){
            $this->connections[$_connectType] = $this->connect($_dbConfig);
        }
        $database = $database ? $database : $_dbConfig['database'];
        $charset = $_dbConfig['charset'] ? $_dbConfig['charset'] : 'UTF8';
        $this->selectDb($database,$this->connections[$_connectType],$charset);
        return $this->dbLink = $this->connections[$_connectType];
    }

    //选择数据库
    private function selectDb($dbName,$connection,$charset='UTF8'){
        $result =  mysql_select_db($dbName,$connection);
        mysql_query('SET NAMES '.strtoupper($charset));
        return $result;
    }

    //判断主从
    private function _isMS($sql){
        $_is = 'slave';
        $sql = trim($sql);
        $_mkws = array('insert','replace','update','delete');
        $_arr = explode(' ',$sql);
        $_kw = $_arr[0];
        foreach ($_mkws as $v){
            if(strtolower($_kw)==$v){
                $_is = 'master';
                break;
            }
        }
        return $_is;
    }

    //执行sql 自动判断主从 $MS 强制指定主从  master slave
    public function dosql($sql,$database='',$MS=''){

        $master = false;
        if($this->isMasterSlave){
            if(!$MS){
                $_connectType = $this->_isMS($sql);
                $master = ($_connectType=='master' || $this->transaction) ? true : false;  
            }else{
                $master = $MS=='master' ? true : false;
            }
            
        }
        $this->initConnect($database,$master);
        $result = $this->_exec($sql);
        if($result){
            $this->numRows = mysql_affected_rows($this->dbLink);
            $this->lastInsertId = mysql_insert_id($this->dbLink);
        }
        return self::$_instance;
    }

    //执行查询语句
    public function query($sql,$database=''){
        $this->initConnect($database,false);
        $result = $this->_exec($sql);
        if($result){
            $this->numRows = mysql_affected_rows($this->dbLink);
        }
        return self::$_instance;
    }

    //执行sql语句
    public function exec($sql,$database=''){
        $this->initConnect($database,true);
        $result = $this->_exec($sql);
        if($result){
            $this->numRows = mysql_affected_rows($this->dbLink);
            $this->lastInsertId = mysql_insert_id($this->dbLink);
        }
        return self::$_instance;
    }

    //所有sql在这执行
    private function _exec($sql){
        $this->sql = $sql;
        if(!$this->dbLink){
            $this->error();
            return false;
        }
        $this->sqlError = '';
        $this->queryResult = mysql_query($sql,$this->dbLink);
        if($this->queryResult===false){
            $this->error();
            return false;
        }else{
            return true;
        }
    }

    private function error(){
        $this->sqlError = mysql_errno().':'.mysql_error($this->dbLink);
        $this->sqlError .="\r\n sql:".$this->sql;
        throw new Exception($this->sqlError);
    }
    
    //开启事务
    public function begin($database=''){
        $sql = 'BEGIN';
        $this->exec($sql,$database);
        $this->transaction = true;
    }

    //提交事务
    public function commit($database=''){
        $sql = 'COMMIT';
        $this->exec($sql,$database);
        $this->transaction = false;
        return true;
    }

    //回滚事务
    public function rollback($database=''){
        $sql = 'ROLLBACK';
        $this->exec($sql,$database);
        $this->transaction = false;
        return true;
    }

    public function fetchRow(){
        if($this->numRows>0){
            $row = mysql_fetch_assoc($this->queryResult);
        }
        return $row;
    }

    public function fetchArray($key=''){
        $rows = array();
        if($this->numRows>0){
            while ($row = mysql_fetch_assoc($this->queryResult)){
                if($key && $row[$key]){
                    $rows[$row[$key]] = $row;
                }else{
                    $rows[] = $row; 
                }
            }
        }
        return $rows;
    }

    public function fetchObect($key=''){
        $rows = array();
        if($this->numRows>0){
            while($row = mysql_fetch_object($this->queryResult)){
                if($key && $row[$key]){
                    $rows[$row[$key]] = $row;
                }else{
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }

    public function fetchFirstColumn(){
        $rows = array();
        if($this->numRows>0){
            while($row = mysql_fetch_row($this->queryResult)){
                $rows[] = $row[0];
            }
        }
        return $rows;
    }

    public function fetchCount(){
        $count = 0;
        if($this->numRows>0){
            $row = mysql_fetch_row($this->queryResult);
            $count = $row[0];
        }
        return $count;
    }

    public function getLastInsertId(){
        return $this->lastInsertId;
    }

    public function getNumRows(){
        return $this->numRows;
    }

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

    public function __destruct(){
        if($this->dbLink){
            mysql_close($this->dbLink);
        }
        $this->dbLink = null;
    }
}
?>