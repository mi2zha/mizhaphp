<?php 
//���ļ���춿��ٜyԇUTF8���a���ļ��ǲ��Ǽ���BOM���K���Ԅ��Ƴ� 
//By Bob Shen 

$basedir="."; //�޸Ĵ��О���Ҫ�z�y��Ŀ䛣��c��ʾ��ǰĿ� 
$auto=1; //�Ƿ��Ԅ��Ƴ��l�F��BOM��Ϣ��1���ǣ�0��� 

//���²��ø�&#21160; 

if ($dh = opendir($basedir)) { 
while (($file = readdir($dh)) !== false) { 
if ($file!='.' && $file!='..' && !is_dir($basedir."/".$file)) echo "filename: $file ".checkBOM("$basedir/$file")." 
"; 
} 
	closedir($dh); 
} 

function checkBOM ($filename) { 
global $auto; 
$contents=file_get_contents($filename); 
$charset[1]=substr($contents, 0, 1); 
$charset[2]=substr($contents, 1, 1); 
$charset[3]=substr($contents, 2, 1); 
if (ord($charset[1])==239 && ord($charset[2])==187 && ord($charset[3])==191) { 
if ($auto==1) { 
$rest=substr($contents, 3); 
rewrite ($filename, $rest); 
return ("BOM found, automatically removed."); 
} else { 
return ("BOM found."); 
} 
} 
else return ("BOM Not Found.<br>"); 
} 

function rewrite ($filename, $data) { 
$filenum=fopen($filename,"w"); 
flock($filenum,LOCK_EX); 
fwrite($filenum,$data); 
fclose($filenum); 
} 
?> 
