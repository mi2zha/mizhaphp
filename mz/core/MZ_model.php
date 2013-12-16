<?php
/********************************
 * 
 *  描述: MZPHP 核心模型类
 *  作者: mizha 
 *  创建: 
 *
 ********************************/
class MZ_Model
{
	/**
	 * @var object 对象单例
	 */
	static $_instance = NULL;

	protected $db = NULL;

	protected $memcache = NULL;

	/**
	 * Model构造函数
	 */
	public function __construct() {}

	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}


	/**
	 * 获取对象唯一实例
	 *
	 * @param void
	 * @return object 返回本对象实例
	 */
	public static function getInstance() {
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * 获取数据库访问对象
	 * @return object
	 */
	public function getDb() {
		try {
			if (is_object($this->db)) {
				return $this->db;
			}
			
			$this->db = new MZ_Db($GLOBALS['masterConf']);
			return $this->db;

		} catch(Exception $e) {
			throw $e;
		}
	}
	
	/**
	 * 获取memcached对象
	 * @return object
	 */	
	function getMemcache() {
		try {
			if (is_object($this->memcache)) {
				return $this->memcache;
			}
			
			$this->memcache = new MZ_Memcache($GLOBALS['memConf']);
			return $this->memcache;

		} catch(Exception $e) {
			throw $e;
		}		
	}
}

?>