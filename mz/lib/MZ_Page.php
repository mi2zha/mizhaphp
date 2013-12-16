<?php

class MZ_Page
{
	/**
     * 
     * @param int $allItemTotal 所有记录数量
     * @param int $currPageNum 当前页数量
     * @param int $pageSize  每页需要显示记录的数量
     * @param string $pageName  当前页面的地址, 如果为空则由系统自动获取,缺省为空
     * @param array $getParamList  页面中需要传递的URL参数数组, 数组中key代表变量民,value代表变量值
     * @return string  返回最后解析出分页HTML代码, 可以直接使用
     * @example 
     *   echo MZ_Page::split(50, 2, 10, 's.php', array('id'=>1, 'name'=>'user'));
     *
     *   输出：第2/50页 上一页 1 2 3 4 5 下一页  跳到 [  ] 页 [GO]
     */
     public static function split($allItemTotal, $currPageNum, $pageSize, $pageName='',  $getParamList = array()){
        if ($allItemTotal == 0) return "";
    
        //页面名称
        $url = $_SERVER['REQUEST_URI'];
		$ServerUri = trim($url, '/');
		$url = explode("/", $ServerUri);
		$url = $url[0] . "/" . $url[1] . "/";
        //参数
        $urlParamStr = "";
        foreach ($getParamList as $key => $val) {
            $urlParamStr .= "&amp;". $key ."=". $val;
        }

        //计算总页数
        $pagesNum = ceil($allItemTotal/$pageSize);
        
        //上一页显示
        $prePage  = ($currPageNum <= 1) ? "上一页" : "<a href='" . ($currPageNum-1) . $urlParamStr ."'  title='上一页' class='page_pre'>上一页</a>";
        
        //下一页显示
        $nextPage = ($currPageNum >= $pagesNum) ? "下一页" : "<a href='" . ($currPageNum+1) . $urlParamStr ."'  title='下一页' class='page_next'>下一页</a>";
        
        //按页显示
        $listNums = "";
        for ($i=($currPageNum-1); $i<($currPageNum+4); $i++) {
            if ($i < 1 || $i > $pagesNum) continue;
            if ($i == $currPageNum) $listNums.= "&nbsp;<span class='page_cur'>".$i."</span>";
            else $listNums.= "&nbsp;<a href='" . $i . $urlParamStr ."' title='第". $i ."页' class='page_other'>". $i ."</a>";
        }
        
        $returnUrl = '<span class="page_text">第'.$currPageNum.'/'.$pagesNum.'页</span> '. $prePage ." ". $listNums ."&nbsp;". $nextPage;
        $gotoForm = ' <span class="page_jump">跳到 <input type="text" class="page_enter" style="width:20px;" id="page_input" value="'. $currPageNum .'" /> 页 <input type="button" value="Go" class="page_submit" onclick="location.href=\'' . '\'+document.getElementById(\'page_input\').value+\''. $urlParamStr .'\'" />';
        
        return $returnUrl . $gotoForm;
    }
	
	
}