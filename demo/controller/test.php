<?php
	class test extends main{
		
		function __construct(){
			parent::__construct();
		}

			
		function index(){
			$a = MZ_model::getInstance()->getDb();
			$n = $a->update('mz_news',array('title'=>'1111'),array('id'=>144));
			$tet = MZ_Model::getInstance()->getMemcache();
			var_dump($cc = $tet->set('bb',1111));
			var_dump($tet->get('bb'));
			exit;
		}
		
		function template(){


			$this->tpl->assign("a", 11);
			$this->tpl->display('a.html');
		}
		
		function page(){
			echo MZ_Page::split(50, $current, 10, '', array());
		}
		


		
	}
?>