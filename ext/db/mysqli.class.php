<?php
class DbMysqli extends DbAbstract
{
    public function __construct($config){
        parent::__construct($config);
    }

    protected function connect(){
        $con = mysqli_init();
        $con->real_connect($this->_dbConfig['host'],$this->_dbConfig['username'],
            $this->_dbConfig['password'],$this->database);
        $charset = $this->_dbConfig['charset'] ? $this->_dbConfig['charset'] : 'UTF8';
        $con->set_charset(strtoupper($charset));
        return $con;
    }

    protected function selectDb(){
        return $this->dbLink->select_db($this->database);
    }

    public function exec($sql,$database=''){
        $this->sql = $sql;
        $this->database = $database;
        $this->initConnect();
        $this->execResult = $this->dbLink->query($this->sql);
        if($this->execResult===FALSE){
            $this->error();
        }
        return self::$_instance;
    }

    public function fetchArray($key=''){
        $rows = array();
        while($row = $this->execResult->fetch_assoc()){
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
        while($row = $this->execResult->fetch_object()){
            if($key && $row->$key){
                $rows[$row->$key] = $row;
            }else{
                $rows[] = $row;
            }
        }
        return $rows;
    }

    public function fetchRow(){
        $row = $this->execResult->fetch_assoc();
        return $row;
    }

    public function fetchFirstColumn(){
        $rows = array();
        while($row = $this->execResult->fetch_row()){
            $rows[] = $row[0];
        }
        return $rows;
    }

    //开启事务
    public function begin($database=''){
        $this->transaction = true;
        return $this->dbLink->begin_transaction();
    }

    //提交事务
    public function commit($database=''){
        $res= $this->dbLink->commit();
        $this->transaction = false;
        return $res;
    }

    //回滚事务
    public function rollback($database=''){
        $res = $this->dbLink->rollback();
        $this->transaction = false;
        return $res;
    }

    public function getLastInsertId(){
        return $this->dbLink->insert_id;
    }


    public function getRowsNum(){
        return $this->dbLink->affected_rows;
    }

    protected function error(){
        $this->sqlError = $this->dbLink->errno.':'.$this->dbLink->error;
        $this->sqlError .="\r\n sql:".$this->sql;
        throw new Exception($this->sqlError);
    }
}