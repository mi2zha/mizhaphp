<?php //-*-coding: utf-8;-*-

function getClientIP()
{
	if( isset($_SERVER) )
	{
		if( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )
		{
			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif ( isset($_SERVER["HTTP_CLIENT_IP"]) ) {
			$realip = $_SERVER["HTTP_CLIENT_IP"];
		} else {
			$realip = $_SERVER["REMOTE_ADDR"];
		}
	} else {
		if( getenv("HTTP_X_FORWARDED_FOR") )
		{
			$realip = getenv("HTTP_X_FORWARDED_FOR");
		} elseif ( getenv("HTTP_CLIENT_IP") ) {
			$realip = getenv("HTTP_CLIENT_IP");
		} else {
			$realip = getenv("REMOTE_ADDR");
		}
    }
    return addslashes($realip);
}

//url redirect
function redirect($uri = '', $method = '302')
{
	$uri = (array) $uri;

	for ($i = 0, $count_uri = count($uri); $i < $count_uri; $i++)
	{
		if (strpos($uri[$i], '://') === FALSE)
		{
			//$uri[$i] = url::site($uri[$i]);
			$uri[$i] = $uri[$i];
		}
	}
	if ($method == '300')
	{
		if ($count_uri > 0)
		{
			header('HTTP/1.1 300 Multiple Choices');
			header('Location: '.$uri[0]);

			$choices = '';
			foreach ($uri as $href)
			{
				$choices .= '<li><a href="'.$href.'">'.$href.'</a></li>';
			}

			exit('<h1>301 - Multiple Choices:</h1><ul>'.$choices.'</ul>');
		}
	}
	else
	{
		$uri = $uri[0];

		if ($method == 'refresh')
		{
			header('Refresh: 0; url='.$uri);
		}
		else
		{
			$codes = array
			(
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'303' => 'See Other',
			'304' => 'Not Modified',
			'305' => 'Use Proxy',
			'307' => 'Temporary Redirect'
			);

			$method = isset($codes[$method]) ? $method : '302';
			header('HTTP/1.1 '.$method.' '.$codes[$method]);
			header('Location: '.$uri);
		}

		exit('<h1>'.$method.' - '.$codes[$method].'</h1><p><a href="'.$uri.'">'.$uri.'</a></p>');
	}
}

function create_token($client_data)
{
	if (empty($client_data) || !is_array($client_data) || count($client_data)<1)
		return false;
	if (!defined('PASSPORT_KEY'))
		return false;
	$str = PASSPORT_KEY;
        //sort($client_data);
        foreach ($client_data as $key=>$val)
            $str .= '|-|'.$val;
        return md5($str);
}

//email格式验证
function valid_email($email)
{
	return (bool) preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string) $email);
}

//tel格式验证

function valid_tel($tel)
{
	return (bool) preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/", (string) $tel);
}

/*
【身份证合法性检查程序】（计算最后一位检验码）
[ 理论 ]
18位身份证：前6位是区位码（表示区域），接下来8位是表示出生日期，接下来3位是本区域的所有当天出生的人的序列号（奇数为男，偶数为女），最后1位是整个前面17位的运算得出的校验码，算法下面有实现。
15位身份证：前6位是区位码，接下来6位是出生日期（没有19），接下来3位是当天出生的人的序列号（奇数为男，偶数为女）
15位转18位：日期前面增加19，然后得出17位，最后通过这个17位运算得到最后1位校验码
*/
function get_idcard_sign($identity)
{
	$wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); 
	$ai = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); 
	$sigma = '';
	for ($i = 0;$i < 17;$i++) { 
	    $sigma += ((int) $identity{$i}) * $wi[$i]; 
	} 
	return $ai[($sigma % 11)]; 
}

function valid_identity($identity)
{
	if( strlen($identity) == 15 )
	{
		return (bool) preg_match("^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$", (string) $identity);
	}
	elseif( strlen($identity) == 18 )
	{
		return (bool) preg_match("^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$", (string) $identity);
	}
	else
	{
		return false;
	} 
}

//获取客户端ip
function get_ip()
{
	// Server keys that could contain the client IP address
	$keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');

	foreach ($keys as $key)
	{
		if (@$ip = $_SERVER[$key])
		{
			$ip_address = $ip;

			// An IP address has been found
			break;
		}
	}

	if ($comma = strrpos($ip_address, ',') !== FALSE)
	{
		$ip_address = substr($ip_address, $comma + 1);
	}

	return $ip_address;
}

/**
 *desc:produce activity no. or verify no.
 *@arg
 *@return string
 */
function produce_no($pre='5ding')
{
	$old = array('0','o','1','i','l');
	$new = array('t','s','e','t','3');

	$len = 11 - mb_strlen($pre);
	$tmp = substr(md5(mt_rand(0,128).microtime()), 4, $len);
	$tmp = str_replace($old,$new,$tmp);

	return $pre.$tmp;
}

/**
 *desc:生成随机码
 *@arg int
 *@return string
 */
function randomkeys($length)
{
  $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
  $key = '';
  for($i=0;$i<$length;$i++)
  {
     $key .= $pattern{rand(0,35)};
  }
  return $key;
}

//检测用户名是否合法
function member_legitimate($username)
{
	return preg_match('/^[A-Za-z][A-Za-z0-9_]{3,17}/', $username);
}
	
/**
 * 功能：获取用户id 对应的name
 * @param $uid
 * @return array(0 => int 1, 1 => string )
 */
function get_user_name ($uid)
{
	if (!$uid) return false;
	$client = new nusoap_client("http://www.5ding.com/interface.php?WSDL", false);
	$token = create_token(array($uid, $_SERVER['REMOTE_ADDR']));
	$par = array('token'=>$token, 'user_id'=>$uid, 'user_ip'=>$_SERVER['REMOTE_ADDR']);
	
	$res = $client->call('iUser.GetByUid', $par);
	if ($res[0]) 
		return $res[1];
	else 
		return false;
}

function crypt_encode($data,$key)
{
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_NOFB);

	mt_srand();
	$iv = mcrypt_create_iv($size, MCRYPT_RAND);
	$tmp = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_NOFB, $iv);
	return base64_encode($iv.$tmp);
}

function crypt_decode($data,$key)
{
	$data = base64_decode($data);
	$size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_NOFB);

	$iv = substr($data, 0, $size);
	$data = substr($data, $size);

	return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_NOFB, $iv), "\0");
}

function dump($arr)
{
	echo '<pre>';
	return var_dump($arr);
}


/**
* 返回用户拥有权限的数组
*
* @param int $ad 用户权限
* @return array 转化成数组，元素则是相应功能权限的ID
*/
function getAD($ad){
	$arr = array();
	while (1==1) {
	   $qx = floor(log($ad,2));   //权限ID
	   $ad = $ad - pow(2,$qx);    //剩余数字
	   $arr[] = $qx;
	   if (!$ad) {
	    break;
	   }
	}
	return $arr;
}

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and
 *           appending the $etc string or inserting $etc into the middle.
 * @link http://smarty.php.net/manual/en/language.modifier.truncate.php
 *          truncate (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function substrs($string, $sublen = 80, $etc = '...',$break_words = false, $middle = false){
    $start=0;
    $code="UTF-8";
       if($code == 'UTF-8')
   {
       $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
       preg_match_all($pa, $string, $t_string);

       if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
       return join('', array_slice($t_string[0], $start, $sublen));
   }
   else
   {
       $start = $start*2;
       $sublen = $sublen*2;
       $strlen = strlen($string);
       $tmpstr = '';

       for($i=0; $i<$strlen; $i++)
       {
           if($i>=$start && $i<($start+$sublen))
           {
               if(ord(substr($string, $i, 1))>129)
               {
                   $tmpstr.= substr($string, $i, 2);
               }
               else
               {
                   $tmpstr.= substr($string, $i, 1);
               }
           }
           if(ord(substr($string, $i, 1))>129) $i++;
       }
       if(strlen($tmpstr)<$strlen ) $tmpstr.= "...";
       return $tmpstr;
   }
}
 /**
  * 二维数组排序（简单）
  * return array
  */
function array_sort($arr_sort='',$arr=array(),$order=SORT_ASC)
{
	foreach ($arr as $val) {
		$arrid[] = $val[$arr_sort];
	}
	array_multisort($arrid, $order, $arr);
	return $arr;
}
// 系统执行时间
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

// 系统执行时间
function microtime_run()
{
	$StartTime = (empty($GLOBALS['StartTime'])) ? microtime_float() : $GLOBALS['StartTime'];
	$EndTime = microtime_float();
	$RunTime = $EndTime - $StartTime;
	return $RunTime;
}
