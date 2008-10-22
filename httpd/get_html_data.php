<?php

require_once '../apps/conf_ini.php';
require_once 'apps/class/Process/CollectHtml/CollectHtmlDao_cls.php';

// 初期化
$oDb = new CollectHtmlDao($oDbConnMng);

// HTML取得先URLリスト取得
$aUrlHeader = $oDb->getUrl();
while($aUrlHeader && count($aUrlHeader) != 0){
	$aHtml = array();
	$aUrlId = array();
	for($iCnt=0;$iCnt<count($aUrlHeader);$iCnt++){
		// アクセスする URL を指定
		echo $aUrlHeader[$iCnt]['url']."\n";
		
		$sHtml = getHtmlData($aUrlHeader[$iCnt]['url']);
		if(!$sHtml) continue;
		
		// UTF-8にエンコード
		$enc = mb_detect_encoding($sHtml);
		$sHtml = mb_convert_encoding($sHtml,"UTF-8",$enc);
		
		// 
		$aHtml[] = array('url_id'=>$aUrlHeader[$iCnt]['url_id'],
							'html'=>$sHtml);
		$aUrlId[] = $aUrlHeader[$iCnt]['url_id'];
		
	}
	
	$oDb->setHtml($aHtml);
	$oDb->updateUrlStatus($aUrlId);
}
?>