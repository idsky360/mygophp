<?php
class DbPdo extends DbAbstract{
	
	public function __construct($config=''){
        parent::__construct($config);
	}

    public function connect(){
        $this->_dbConfig['charset'] = $this->_dbConfig['charset'] ? $this->_dbConfig['charset'] : 'UTF8';
        $dsn = 'mysql:host='.$this->_dbConfig['host'].';port='.$this->_dbConfig['port'].';';
        $dsn .= 'dbname='.$this->database.';charset='.$this->_dbConfig['charset'];
    	$con = new PDO($dsn,$this->_dbConfig['username'],$this->_dbConfig['password']);
        return $con;
    }

    public function selectDb(){
        return $this->dbLink->exec("use ".$this->database.";");
    }

    public function exec($sql,$database=''){
        $this->sql=$sql;
        $this->database = $database;
        $this->initConnect();
        $this->execResult = $this->dbLink->query($this->sql);
        if($this->execResult===false){
            $this->error();
        }
        return self::$_instance;
    }

    public function fetchArray($key=''){
        $all = $this->execResult->fetchAll(PDO::FETCH_ASSOC);
        if($key){
            $rows = array();
            foreach ($all as $row) {
                if($row[$key]) $rows[$key] = $row;
            }
        }else{
            $rows = $all;
        }
        return $rows;
    }

    public function fetchObject($key=''){
        $all = $this->execResult->fetchAll(PDO::FETCH_OBJ);
        if($key){
            $rows = array();
            foreach($all as $row){
                if($row->$key) $rows[$row->$key] = $row;
            }
        }else{
            $rows = $all;
        }
        return $rows;
    }

    public function fetchRow(){
        return $this->execResult->fetch(PDO::FETCH_ASSOC); 
    }

    public function fetchFirstColumn(){
        $all = $this->execResult->fetchAll(PDO::FETCH_NUM);
        $rows = array();
        foreach($all as $row){
            $rows[] = $row[0];
        }
        return $rows;
    }

    public function begin(){
        $this->dbLink->beginTransaction();
        $this->transaction = true;
    }

    public function commit(){
        $this->dbLink->commit();
        $this->transaction = false;
    }

    public function rollback(){
        $this->dbLink->rollback();
        $this->transaction = false;
    }

    public function getLastInsertId(){
        return $this->dbLink->lastInsertId();
    }

    public function getRowsNum(){
        return $this->execResult->rowCount();
    }

    public function error(){
        $errorInfo = $this->dbLink->errorInfo();
        $this->sqlError = $errorInfo[1].":".$errorInfo[2]."\r\n";
        $this->sqlError .=" sql:".$this->sql;
        throw new Exception($this->sqlError);
    }
}