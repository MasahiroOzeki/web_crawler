<?php
// 
define("COMMON_DOCUMENT_ROOT","C:/Documents and Settings/ozekim/workspace/web_crawler/");

// Includeパス
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT);
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT.'apps/');
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT.'apps/lib/');
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT.'apps/lib/PEAR/');

// インクルード
require_once('XML_HTMLSax.php');
require_once('Handler.php');
//require_once('class/DB/DBConnMng_cls.php');
require_once('common_function.php');

// 実行設定
set_time_limit(6000);
ini_set("memory_limit","512M"); 

// DB設定
$aDBInfo[1]['DB_KIND'] = 'postgres';
$aDBInfo[1]['DB_HOST'] = 'localhost';
$aDBInfo[1]['DB_PORT'] = 5432;
$aDBInfo[1]['DB_USER'] = 'ozeki';
$aDBInfo[1]['DB_PASS'] = 'goodluck00';
$aDBInfo[1]['DB_NAME'] = 'snoopy';

//
$oDbConnMng = new DBConnMng($aDBInfo);

?>