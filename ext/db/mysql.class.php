<?php
/*
*数据库 mysql驱动类
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class DbMysql extends DbAbstract {

    //构造函数
	public function __construct($config=''){
        parent::__construct($config);
	}
    
    //创建数据库链接
    protected function connect(){
        $con = @mysql_connect($this->_dbConfig['host'].':'.$this->_dbConfig['port'],$this->_dbConfig['username'],$this->_dbConfig['password']);
        if(!$con){
            throw new Exception('mysql connect error!'); 
        }
        $charset = $this->_dbConfig['charset'] ? $this->_dbConfig['charset'] : 'UTF8';
        mysql_set_charset(strtoupper($charset),$con);
        return $con;
    }

    //选择数据库
    protected function selectDb(){
        $result =  mysql_select_db($this->database,$this->dbLink);
        return $result;
    }

    //执行sql语句
    public function exec($sql,$database=''){
        $this->sql = $sql;
        $this->database = $database;
        $this->initConnect();
        $this->execResult = @mysql_query($sql,$this->dbLink);
        if($this->execResult===false){
            $this->error();
        }
        return self::$_instance;
    }

    public function fetchArray($key=''){
        $rows = array();
        while ($row = mysql_fetch_assoc($this->execResult)){
            if($key && $row[$key]){
                $rows[$key] = $row;
            }else{
                $rows[] = $row; 
            }
        }
        return $rows;
    }

    public function fetchObject($key=''){
        $rows = array();
        while($row = mysql_fetch_object($this->execResult)){
            if($key && $row->$key){
                $rows[$row->$key] = $row;
            }else{
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function fetchRow(){
        $row = mysql_fetch_assoc($this->execResult);
        return $row;
    }

    public function fetchFirstColumn(){
        $rows = array();
        while($row = mysql_fetch_row($this->queryResult)){
            $rows[] = $row[0];
        }
        return $rows;
    }

    //开启事务
    public function begin($database=''){
        $sql = 'BEGIN';
        $this->transaction = true;
        return $this->exec($sql,$database);
    }

    //提交事务
    public function commit($database=''){
        $sql = 'COMMIT';
        $res = $this->exec($sql,$database);
        $this->transaction = false;
        return $res;
    }

    //回滚事务
    public function rollback($database=''){
        $sql = 'ROLLBACK';
        $res = $this->exec($sql,$database);
        $this->transaction = false;
        return $res;
    }

    public function getLastInsertId(){
        return mysql_insert_id();
    }


    public function getRowsNum(){
        return mysql_affected_rows();
    }

    protected function error(){
        $this->sqlError = mysql_errno().':'.mysql_error($this->dbLink);
        $this->sqlError .="\r\n sql:".$this->sql;
        throw new Exception($this->sqlError);
    }
}
?>