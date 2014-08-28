<?php
/*
*sql生成器
*@copyright 2014 http://idsky.net
*@author idsky<idsky360@gmail.com>
*/
class sqlBuilder{

	protected $comparison = array('eq'=>'=','neq'=>'<>','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','in'=>'IN','notin'=>'NOT IN');
	protected $selectSql  = 'SELECT%DISTINCT% %FIELD% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDERBY%%LIMIT%';
	protected $logic = array('AND','OR');
	
	//构造函数
	public function __construct(){

	}

	//生成insert语句
	public  function buildInsertSql($data,$options){
		$fields = $values = array();
		foreach($data as $k=>$v){
			$fields[] = $this->parseKey($k);
			$values[] = $this->parseValue($v);
		}
		$sql = 'INSERT INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
        $_comment = $this->parseComment($options['comment']);
        $sql .= $_comment ? ' '.$_comment : '';
        return $sql;
	}	

	//生成replace语句
	public  function buildReplaceSql($data,$options){
		$fields = $values = array();
		foreach($data as $k=>$v){
			$fields[] = $this->parseKey($k);
			$values[] = $this->parseValue($v);
		}
		$sql = 'REPLACE INTO '.$this->parseTable($options['table']).' ('.implode(',', $fields).') VALUES ('.implode(',', $values).')';
        $_comment = $this->parseComment($options['comment']);
        $sql .= $_comment ? ' '.$_comment : '';
        return $sql;
	}

	//生成update语句
	public function buildUpdateSql($data,$where,$options){
		$sql = 'UPDATE '.$this->parseTable($options['table']);
		$sql .= $this->parseSet($data);
        $sql .= $this->parseWhere($where);
        $sql .= $this->parseOrderBy($options['orderby']);
        $sql .= $this->parseLimit($options['limit']);
        $sql .= $this->parseComment($options['comment']);
		return $sql;
	}

    //生成select语句 暂不支持 union
    public function buildSelectSql($fields,$where,$options){
        $sql   = str_replace(
        array('%DISTINCT%','%FIELD%','%TABLE%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDERBY%','%LIMIT%'),
        array(
            $this->parseDistinct(isset($options['distinct'])?$options['distinct']:false),
            $this->parseFields($options['field']),
            $this->parseTable($options['table']),
            $this->parseJoin($options['join']),
            $this->parseWhere($where),
            $this->parseGroup($options['group']),
            $this->parseHaving($options['having']),
            $this->parseOrderBy($options['orderby']),
            $this->parseLimit($options['limit']),
        ),$this->selectSql);
        $sql .= $this->parseLock($options['lock']);
        $sql .= $this->parseComment($options['comment']);
        return $sql;
    }

    //生成delete语句
    public function buildDeleteSql($where,$options){
        $sql = 'DELETE FROM ';
        $sql .= $this->parseTable($options['table']);
        $sql .= $this->parseWhere($where);
        $sql .= $this->parseGroup($options['group']);
        $sql .= $this->parseHaving($options['having']);
        $sql .= $this->parseOrderBy($options['orderby']);
        $sql .= $this->parseLimit($options['limit']);
        return $sql;
    }
	
    //生成count语句
    public function buildCountSql($where,$options){
        $sql = 'SELECT COUNT(*) FROM ';
        $sql .= $this->parseTable($options['table']);
        $sql .= $this->parseWhere($where);
        return $sql;
    }

    protected function parseTable($tables) {
        if(is_array($tables)) {// 支持别名定义
            $array   =  array();
            foreach ($tables as $table=>$alias){
                if(!is_numeric($table))
                    $array[] =  $this->parseKey($table).' '.$this->parseKey($alias);
                else
                    $array[] =  $this->parseKey($table);
            }
            $tables  =  $array;
        }elseif(is_string($tables)){
            $tables  =  explode(',',$tables);
        }
        $tables = implode(',',$tables);
        return $tables;
    }

    protected function parseLock($lock=false){
        if(!$lock) return '';
        return ' FOR UPDATE ';
    }

    protected function parseComment($comment){
        return  !empty($comment)?   ' /* '.$comment.' */':'';
    }

   	protected function parseKey(&$key) {
        return $key;
    }

    protected function parseValue($value){
        if(is_string($value)) {
            $value =  '\''.addslashes($value).'\'';
        }elseif(is_array($value)) {
            $value =  array_map(array($this, 'parseValue'),$value);
        }elseif(is_bool($value)){
            $value =  $value ? '1' : '0';
        }elseif(is_null($value)){
            $value =  null;
        }
        return $value;
    }

    protected function parseSet($data){
    	$set = array();
    	$setStr = '';
    	if(is_string($data)){
    		$setStr = '';
    	}elseif(is_array($data)){
    		foreach($data as $k=>$v){
    			if(is_array($v)){
    				$v['value'] = $v['value'] ? $v['value'] : 1;
    				if(isset($v['operator']) && in_array($v['operator'],array('+','-','*'))){
    					$set[] = $this->parseKey($k).'='.$this->parseValue($k.$v['operator'].$v['value']);
    				}
    			}else{
    				$set[] = $this->parseKey($k).'='.$this->parseValue($v);
    			}
    		}
    		$setStr = implode(',',$set);
    	}
    	return ' SET '.$setStr;
    }

    protected function parseWhere($where){
    	$whereStr = '';
        if(!$where) return $whereStr;
    	if(is_string($where)){
    		$whereStr = $where;
    	}elseif(is_array($where)){
    		$i = 0;
    		$_operators = array('>','<','=','>=','<=','<>');
    		foreach($where as $k=>$v){
    			if(is_array($v)){
                    //默认operator为 = 
                    if(empty($v['operator'])) $v['operator'] = '=';
                    //默认logic 为 and 
                    if(empty($v['logic'])) $v['logic'] = 'and';
	    			$operator = in_array($v['operator'],$_operators) ? $v['operator'] : $this->comparison[$v['operator']];
	    			if($v['operator']){
	    				if($i && in_array(strtoupper($v['logic']),$this->logic)){
	    					$whereStr .= ' '.strtoupper($v['logic']);
	    				}

	    				if(in_array($v['operator'],array('like','notlike'))){
	    					$whereStr .= ' '.$this->parseKey($k).' '.$operator.' %'.$this->parseValue($v['value']).'%';
	    				}elseif(in_array($v['operator'],array('in','notin'))){
	    					if(is_array($v['value'])){
	    						$whereStr .= ' '.$this->parseKey($k).' '.$operator.' ('.implode(',',$v['value']).')'; 
	    					}
	    				}else{
	    					$whereStr .= ' '.$this->parseKey($k).$operator.$this->parseValue($v['value']);
	    				}
	    			}
    			}else{
                    if($i){
                        $whereStr .='  AND';
                    }
                    $whereStr .= ' '.$this->parseKey($k).'='.$this->parseValue($v);
                }
                $i++;
    		}
    	}
    	return ' WHERE '.$whereStr;
    }

    protected function parseOrderBy($orderby){
        $orderbyStr = '';
        if(!$orderby) return $orderbyStr;
        if(is_array($orderby)){
            $_ody = array();
            foreach($orderby as $k=>$v){
                if(is_numeric($k)){
                    $ody[] = $this->parseKey($v);
                }else{
                    $ody[] = $this->parseKey($k).' '.$this->parseKey($v);
                }
            }
            $orderbyStr .= implode(',',$ody);
        }else{
            $orderby = str_replace(array('desc','asc'),array('DESC','ASC'),$orderby);
            $orderbyStr .= $this->parseKey($orderby);
        }
        return !empty($orderbyStr) ? ' ORDER BY '.$orderbyStr : '';
    }

    protected function parseLimit($limit){
        $limitStr = '';
        if(!$limit) return $limitStr;
        if(is_array($limit)){
            $s = $limit[0] ? $limit[0] : 0;
            $n = $limit[1] ? $limit[1] : 100;
            $limitStr .= $s.','.$n;
        }else{
            $limitStr .= $limit;
        }
        return $limitStr ? ' LIMIT '.$limitStr : '';
    }

    protected function parseFields($fields){
        $fieldStr = '';
        $fieldArr = array();
        if(is_array($fields)){
            foreach($fields as $k=>$v){
                //支持字段别名 
                if(is_numeric($k)){
                    $fieldArr[] = $this->parseKey($v);
                }else{
                    $fieldArr[] = $this->parseKey($k).' '.$this->parseValue($v);
                }
            }
            $fieldStr = implode(',',$fieldArr);
        }else{
            $fieldStr = $fields;
        }
        return $fieldStr ? $fieldStr : '*';
    }

    protected function parseDistinct($distinct){
        return $distinct ? ' DISTINCT' : '';
    }

    protected function parseGroup($group) {
        return $group ? ' GROUP BY '.$group : '';
    }

    protected function parseHaving($having) {
        return  $having ? ' HAVING '.$having : '';
    }

    protected function parseJoin($join) {
        $joinStr = '';
        if($join) {
            $joinStr = implode(' ',$join);
        }
        return $joinStr ? ' '.$joinStr : '';
    }

}
?>