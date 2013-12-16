<?php
//应用路径

define("APP_CONTROLLER_DIR", APP_DIR . "controller/");
define("APP_MODEL_DIR", APP_DIR . "model/");

class MZ_Router
{
	/**
	 * @var object 对象单例
	 */
	static $_instance = NULL;


	private function __construct() {}

	/**
	 * 保证对象不被clone
	 */
	private function __clone() {}

	public static function getInstance() {
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * 获取处理请求的URL路径 (SERVER_URI) 方式
	 * @return array 返回parse好的数组
	 */
	public static function parseUri() {

		//解析URI
		$route = array();

		$ServerUri = explode("?", $_SERVER['REQUEST_URI']);
		$ServerUri = trim($ServerUri[0], '/');
		$module = explode("/", $ServerUri);
		$defaultController = 'index';
		$defaultAction = 'index';
		//支持URL打开和没打开的情况
		if ($ServerUri[0] == '/') {
			$route['ControllerName'] = isset($_GET['c']) ? $_GET['c'] : $defaultController;
			$route['ActionName']	 = isset($_GET['a']) ? $_GET['a'] : $defaultAction;	
		} else {
			$route['ControllerName'] = isset($module[0]) && $module[0]!='' ? $module[0] : $defaultController;
			$route['ActionName']	 = isset($module[1]) && $module[1]!='' ? $module[1] : $defaultAction;
		}
		foreach ($module as $key=>$value) {
			$_GET[$key] = $value;
		}
		return $route;
	}


	/**
	 * 路由分发
	 */
	public function dispatch() {
		//不进行魔术过滤
		set_magic_quotes_runtime(0);

		try {

			//读取控制器和action
			$route = self::parseUri();
			$controller = $route['ControllerName'];
			$action		= $route['ActionName'];

			//包含控制器文件
			$controllerFile = APP_CONTROLLER_DIR . $controller . ".php";
			if ( !is_file($controllerFile) || !is_readable($controllerFile) ) {
				throw new Exception("controller file $controllerFile not exist or not readable");
			}
			require($controllerFile);
			if ( !class_exists($controller, false) ) {
				throw new Exception("controller class $controller  not exist");
			}

			//判断 Action
			$con = new $controller($controller, $action);
			if ( !method_exists($con, $action) ) {
				throw new Exception("controller class method " . $controller->$action() . " not exist");
			}

			//进行Action操作
			return $con->$action();

		} catch (Exception $exception) {
			throw $exception;
		}
	}
}



/**
 * 类自动包含功能
 *
 * @param string 需要包含的类名
 * @return void
 */
function __autoload($class) {
	$core		= CORE_PATH .$class . ".php";
	$lib		= LIB_PATH . $class . ".php";
	$controller = APP_CONTROLLER_DIR . $class . ".php";
	$model		= APP_MODEL_DIR . $class . ".php";
	$inc		= '';

	//从框架中载入
   if (is_file($core)) {
		$inc = $core;
	}
	//从基础类中载入
	if (is_file($lib)) {
		$inc = $lib;
	}
	//从控制器载入
	elseif (is_file($controller)) {
		$inc = $controller;
	}
	//从model载入
	elseif (is_file($model)) {
		$inc = $model;
	}

	//如果没有找到文件
	if ($inc == '')	return false;

	//包含文件
	require_once($inc);
}

