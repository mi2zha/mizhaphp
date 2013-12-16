<?php
//判断版本
if (PHP_VERSION < '5.0.0') {
	die("MZPHP Error: Please use PHP Version >= 5.0");
}

/* 初始化设置 -- 针对虚拟机*/
/*
@ini_set('memory_limit',          '128M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);
@ini_set("arg_separator.output","&amp;");
ini_set('date.timezone','Asia/Shanghai');
*/

error_reporting(E_ALL);
//error_reporting(0);

// 要求定义好 _PATH_ 和 SCRIPT_NAME ，及定义 DS 为路径分隔符
if (!defined('DS'))	{
	die("Error : (".realpath(__FILE__).") not defined 'DS' "); 
}
if (!defined('ROOT_PATH')) {
	die("Error : (".realpath(__FILE__).") not defined 'ROOT_PATH' "); 
}
//判断是否定义APP_DIR
if (!defined("APP_DIR")) {
	die("MZPHP Error: Please define APP_DIR for app_name/view/index.php");
}
//应用框架视图路径
define("APP_VIEW_DIR", APP_DIR ."/templates/tpl/");
//应用框架配置文件路径
define("CONFIG_PATH", APP_DIR . 'config' . DS);
//框架
define("INCLUDE_PATH", ROOT_PATH . 'include' . DS);
define("LIB_PATH", ROOT_PATH . 'lib' . DS);
define("CORE_PATH", ROOT_PATH . 'core' . DS);

require (CORE_PATH . 'MZ_Router.php');
require (CONFIG_PATH . 'config.db.php');