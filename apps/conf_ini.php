<?php
define("COMMON_DOCUMENT_ROOT","C:/Documents and Settings/ozekim/workspace/web_crawler/");

ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT);
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT.'apps/');
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT.'apps/lib/');
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.COMMON_DOCUMENT_ROOT.'apps/lib/PEAR/');

// インクルード
require_once('XML_HTMLSax.php');

require_once('Handler.php');
//require_once('DB/PdoDataBaseMng_cls.php');

require_once('common_function.php');

// DB設定
$sDBName['DB_NAME'] = 'snoopy';
$sDBName['DB_USER'] = 'ozeki';
$sDBName['DB_PASS'] = 'goodluck00';
$sDBName['DB_HOST'] = 'localhost';
$sDBName['DB_PORT'] = 5432;
$sDBName['DB_KIND'] = 'postgres';

set_time_limit(6000);
ini_set("memory_limit","512M"); 

?>