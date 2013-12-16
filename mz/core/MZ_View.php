<?php
/********************************
 * 
 *  描述: MZPHP 核心视图类
 *  作者: mizha 
 *  创建: 对原生的php/smarty/模板进行支持
 *
 ********************************/


class MZ_View 
{
	/**
	 * @var 视图类型为原生PHP
	 */
	const TYPE_PHP		= 'php';
	
	/**
	 * @var 视图类型为Smarty
	 */
	const TYPE_SMARTY	= 'smarty';

	/**
	 * @var object 对象单例
	 */
	static $_instance = NULL;

	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}

    /**
	 * 构造函数
	 */
	private function __construct() {}


	/**
	 * 视图工厂操作方法
	 *
	 * @param string $viewType 
	 * @param string $params 
	 * @return object
	 */
	public static function factory( $viewType = '', $params = array() ) {
		$viewType = strtolower($viewType);
		if ($viewType == '') {
			$viewType = self::TYPE_PHP;
		}
		switch($viewType) {
			case self::TYPE_PHP:
				$obj = MZ_View_Php::getInstance();
				break;
			case self::TYPE_SMARTY:
				$obj = MZ_View_Smarty::getInstance($params)->getSmarty();
				break;
			default:
				throw new Exception("View type $viewType not support");
		}
		return $obj;
	}



}



/**
 * 原生PHP文件模板视图类
 *
 * @desc 使用PHP原生程序作为模板
 */
class MZ_View_Php
{
	/**
	 * @var object 对象单例
	 */
	static $_instance = NULL;
	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}
    /**
	 * 构造函数
	 */
	private function __construct() {}

	/**
	 * 获取对象唯一实例
	 *
	 * @param void
	 * @return object 返回本对象实例
	 */
	public static function getInstance($params) {
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
 
	/**
	 * 解析处理一个模板文件
	 *
	 * @param  string $filePath  模板文件路径
	 * @param  array  $vars 需要给模板变量赋值的变量
	 * @return void
	 */
	public function renderFile($filePath, $vars) {
		$filePath = APP_VIEW_DIR . $filePath;
		if(!is_file($filePath) || !is_readable($filePath)) {
			throw new Exception("View file ". $filePath ." not exist or not readable");
		}
		if (!empty($vars)) {
			foreach($vars as $key => $value) { 
				$$key=$value; 
			}
		}
		require_once($filePath);
	}

}




/**
 * Smarty文件模板视图类
 *
 * @desc 针对 Smarty Template 的模板View的模板加载
 *
 * 相关链接：
 *	Smarty官网：http://www.smarty.net/
 *	Smarty手册：http://www.phpchina.com/manual/smarty/
 *	Smarty入门：http://www.google.cn/search?q=%E8%8F%9C%E9%B8%9F%E5%AD%A6PHP%E4%B9%8BSmarty%E5%85%A5%E9%97%A8&btnG=Google+%E6%90%9C%E7%B4%A2
 */
class MZ_View_Smarty
{

	/**
	 * @var object 对象单例
	 */
	static $_instance = NULL;
	/**
	 * @var array Smarty对象参数
	 */
	//public $params = array();
	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}

    /**
	 * 构造函数
	 *
	 * @param object $controller 控制器对象
	 *
	 * @param array $params 需要传递的选项参数
	 *
	 * 参数说明：
	 * 
	 * */
//    public $params = array(
//		'template_dir'		=> '/templates/tpl/',	    //指定模板文件存放目录，缺省为 /templates/tpl/ 目录
//		'cache_dir'			=> '/templates/cache/',	//指定缓存文件存放目录
//		'compile_dir'		=> '/templates/compile/',			//Smarty编译目录
//		'caching'		    => false,				    //Smarty缓存，缺省为false
//		'cache_lifetime'    => 3600,				    //Smarty缓存时间，单位秒
//		'cache_dir'         => 3600,				    //Smarty缓存文件目录
//		'left_delimiter'	=> '{*',					//模板变量的左边界定符, 缺省为 {*
//		'right_delimiter'	=> '*}',					//模板变量的右边界定符，缺省为 *}
//	   );

	 
	private function __construct($params) {
		$this->params = $params;
	}


	/**
	 * 获取对象唯一实例
	 *
	 * @param void
	 * @return object 返回本对象实例
	 */
	public static function getInstance($params) {
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self($params);
		}
		return self::$_instance;
	}
	
	/**
	 * 解析处理一个模板文件
	 *
	 * @param  string $filePath  模板文件路径
	 * @param  array  $vars 需要给模板变量赋值的变量
	 * @return void
	 */
	public function getSmarty() {
		//加载Smarty
		$smarty = new MZ_smarty;

		//判断是否传递配置参数
		if ( empty($this->params) || !isset($this->params['compile_dir']) || !isset($this->params['cache_dir'])) {
			throw new Exception("Smarty template engine configure [compile_dir,config_dir,cache_dir] not set, please  MZ_View->factory() entry params"); 
		}

		//设置Smarty参数
		$smarty->template_dir 	 = !isset($this->params['template_dir']) ? APP_DIR . 'templates' . DS . 'tpl' . DS : $this->params['template_dir'];
		$smarty->compile_dir  	 = !isset($this->params['compile_dir']) ? APP_DIR . 'templates' . DS . 'tpl' . DS : $this->params['compile_dir']; 
		$smarty->caching		 = false;
		$smarty->cache_lifetime	 = 3600;
		$smarty->cache_dir    	 = !isset($this->params['cache_dir']) ? APP_DIR . 'templates' . DS . 'cache' . DS : $this->params['cache_dir'];
		$smarty->left_delimiter  = !isset($this->params['left_delimiter']) ? "{%" : $this->params['left_delimiter'];
		$smarty->right_delimiter = !isset($this->params['right_delimiter']) ? "%}" : $this->params['right_delimiter'];		
		
		return $smarty;
	}


}

?>