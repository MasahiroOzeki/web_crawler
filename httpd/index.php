<?php

require_once '../apps/conf_ini.php';

$oParser = new HtmlParse();

// アクセスする URL を指定
$URL = 'http://perldoc.jp/docs/modules/DBD-mysql-2.1026/DBD/mysql/INSTALL.pod';
$sHtml = getHtmlData($URL);

//UTF-8にエンコード
$enc = mb_detect_encoding($sHtml);
$sHtml = mb_convert_encoding($sHtml,"UTF-8",$enc);

//解析
$rtn = $oParser->execHtmlParse($sHtml);

if($rtn !== false){
	list($aSubject,$aUrl,$aNonTagHtml) = $rtn;
	$fp = fopen("C:/body.txt", 'w');
	
	$sLine = implode('',$aNonTagHtml);
	fwrite($fp,mb_convert_encoding($sLine,"SJIS","UTF-8"));
	
	$flag = fclose($fp);
}

?>