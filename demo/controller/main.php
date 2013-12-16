<?php

class main extends MZ_model
{
	function __construct(){
		$params = array(
						'template_dir'		=> APP_DIR . '/templates/tpl/',	    //指定模板文件存放目录，缺省为 /templates/tpl/ 目录
						'cache_dir'			=> APP_DIR . '/templates/cache/',	//指定缓存文件存放目录
						'compile_dir'		=> APP_DIR . '/templates/compile/',			//Smarty编译目录
					);
		$this->tpl = MZ_View::factory('smarty', $params);		
	}
}
?>