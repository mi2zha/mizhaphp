<?php

/**
 * MySQL Master/Slave数据库读写操作类  0.01版本
 * 开发作者：米渣  
 * 功能描述：自动支持Master/Slave 读/写 分离操作，支持多Slave主机 
 * 日期：
 */

class MZ_Db{
	
	public $masterConfig = array();
	
	public $slaveConfig = array();
	
	protected $isOne = true;
	
	protected $dbCharset = "UTF8";

	protected $_tablePrefix = '';
	
	protected $wConf = array();
	
	protected $rConf = array();
	
	protected $_select = array();
	protected $_from = array();
	protected $_where = array();
	protected $_group = array();
	protected $_order = array();
	
	function MZ_Db($masterConfig, $slaveConfig=array()) {
        if (is_array($masterConfig) && !empty($masterConfig)) {
            $this->wConf = $masterConfig;   
        }   
        if (!is_array($slaveConfig) || empty($slaveConfig)) {
            $this->wConf = $masterConfig;
        } else {
            $this->rConf = $slaveConfig;
        }
	}
	
	function connect($dbHost, $dbUser, $dbpwd, $dbDataBase) {
        //连接数据库主机
        $conn = mysql_connect($dbHost, $dbUser, $dbpwd);
        if(!mysql_select_db($dbDataBase, $conn)) {
            return false;
        }
        if(!mysql_query("SET NAMES '" . $this->dbCharset . "'", $conn)) {
        	return false;
        }
        return $conn;
	}
	
	function masterConn() {
		$db = $this->connect($this->wConf['host'],$this->wConf['user'],$this->wConf['pwd'],$this->wConf['db']);
		return $db;
	}
	
	function slaveConn() {
		$arrHost = explode("|", $this->rConf['host']);
        if (!is_array($arrHost) || empty($arrHost)) {
            return $this->masterConn();
        }
        foreach($arrHost as $tmpHost) {
			$db = $this->connect($this->rConf['host'],$this->rConf['user'],$this->rConf['pwd'],$this->rConf['db']);
            if ($db && is_resource($db)) {
                $this->rConn[] = $db;
            }
        }
        //随机在已连接的Slave机中选择一台   
        $key = array_rand($this->rdbConn);
        if (isset($this->rConn[$key]) && is_resource($this->rConn[$key])) {
            return $this->rConn[$key];
        }
        //如果选择的slave机器是无效的，并且可用的slave机器大于一台则循环遍历所有能用的slave机器   
        if (count($this->rConn) > 1) {
            foreach($this->rConn as $conn) {
                if (is_resource($conn)) {
                    return $conn;
                }
            }
        }
        return $this->masterConn();
	}
	
	function select($field = '*') {
		$this->_select = $field;
		return $this;
	}
	
	function from($table) {
		$this->_tableName = $table;
		return $this;
	}
	
	function where($where=array(), $type='AND') {
		foreach($where AS $key=>$val) {
			$prefix = (count($this->_where) == 0) ? '' : $type . ' ';
			$key .= ' =';
			$this->_where[] = $prefix . $key . ' ' . $val;
		}
		return $this;
	}
	
	function getOne( $table, $field='*', $where=array() ) {
		$query = $this->select($field)->from($table)->where($where)->_base_sql();
		$rs = $this->_fetch_array($query);
		return $rs;
	}
	
	function getAll( $table, $field='*', $where = array() ) {
		$query = $this->select($field)->from($table)->where($where)->_base_sql();
		while( $row = $this->_fetch_array($query) ) {
			$rs[] = $row;
		}
		return $rs;
	}
	
	function update( $table, $data=array() , $where=array() ) {
		if(!is_array($data) || !is_array($where) || empty($data) || empty($where)) die('false');
		$fields = array();
		$wheres = array();
		foreach($data as $key => $val)
		{
			$fields[] = '`' . $key . "` = " . $val;
		}
		foreach($where as $key => $val)
		{
			$wheres[] = '`' . $key . "` = " . $val;
		}
		$sql = 'UPDATE ' . $table . ' SET ' . implode(', ',$fields) . ' WHERE ' . implode(" ", $wheres);
		if(!$this->_query($sql)) {
			return false;
		}
		return $this->_affected_rows();
	}
	
	function insert($table, $data) {
		$fields = array();
		$values = array();

		foreach($data as $key => $val) {
			$fields[] = '`' . $key . '`';
			$values[] = $val;
		}
		$sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
		if(!$this->_query($sql, true)) {
			return false;
		}
		return $this->_lastID();
	}
	
	function delete($table, $where) {
		$sql = 'DELETE FROM  ' . $table . ' WHERE ' . implode(" ", $where);
		if( !$this->_query($sql) ) {
			return false;
		}
		return $this->_affected_rows();		
	}
	
	function exec($sql) {
		if(!$this->isSelect($sql) || $this->isOne) {
			$dbConn = $this->masterConn();
			if($this->isSelect($sql)) {
				$query = mysql_query($sql,$dbConn);
				return $this->_fetch_array($query);
			}else{
				mysql_query($sql,$dbConn);
				return $this->_lastId();
			}
		}else{
			$dbConn = $this->slaveConn();
			return mysql_query($sql,$dbConn);
		}
	}
	
	function isSelect($sql) {
		if( trim(strtolower(substr(ltrim($sql), 0, 6))) == 'select' )
		return true;
	}
	function _lastId() {
        if (($lastId = mysql_insert_id()) > 0) {   
            return $lastId;   
        }   
        return $this->getOne("SELECT LAST_INSERT_ID()", '', true);   
	}
	
	function table($table) {
		if(isset($table)) {
			return '`' .$table. '`';
		} else {
			die('Table is not Empty');
		}
	}
	
	function _escape_string($str = '', $conn) {
		return mysql_real_escape_string($str);
	}
	
	function _affected_rows() {
		return mysql_affected_rows();
	}
	
	function _fetch_array($result='', $type=MYSQL_ASSOC) {
		return mysql_fetch_array($result, $type);
	}
	
	function _base_sql() {
		$sql = "SELECT ";
		
		$sql .= !$this->_select ? '*' : $this->_select;
		
		$sql .= " FROM ";
		
		$sql .= $this->_tableName;
		
		$sql .= " ";
		
		if (count($this->_where) > 0)
		{
			$sql .= " WHERE ";
		}
		
		$sql .= implode(" ", $this->_where);
		
		if (count($this->_group) > 0)
		{
			$sql .= "GROUP BY ";

			$sql .= implode(', ', $this->_group);
		}

		if (count($this->_order) > 0)
		{
			$sql .= " ORDER BY ";
			$sql .= implode(', ', $this->_order);
		}		
		
		return $this->_query($sql);
	}
	
	function _query($sql) {
		if(!$this->isSelect($sql) || $this->isOne) {
			$dbConn = $this->masterConn();
			return mysql_query($sql,$dbConn);
		}else{
			$dbConn = $this->slaveConn();
			return mysql_query($sql,$dbConn);
		}
	}
	
}