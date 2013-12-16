<?php

define('DS', DIRECTORY_SEPARATOR);

define( "APP_DIR", realpath(dirname(__FILE__) . DS . '..') .DS );		//应用路径
define( "ROOT_PATH", realpath(realpath(APP_DIR) . '..' .DS . '..' . DS . 'mz') . DS );	//框架路径

//设定包含文件路径
set_include_path(get_include_path() . PATH_SEPARATOR .ROOT_PATH);

require(ROOT_PATH . 'mizha.php');

//分发处理
try {
	MZ_Router::getInstance()->dispatch(); 
} catch (Exception $e) {
	echo $e->getMessage(); exit;
}
