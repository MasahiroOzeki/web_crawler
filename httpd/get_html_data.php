<?php

require_once '../apps/conf_ini.php';
require_once 'apps/class/Process/CollectUrl/CollectHtmlDao_cls.php';

// 初期化
$oDb = new CollectHtmlDao($oDbConnMng);

for($iHierarchy=0;$iHierarchy<3;$iHierarchy++){
	// HTML取得先URLリスト取得
	$aUrlHeader = $oDb->getUrl();
	
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
							'html',$sHtml);
		$aUrlId[] = $aUrlHeader[$iCnt]['url_id'];
		
	}
	
	$oDb->setHtml($aHtml);
	$oDb->updateUrlStatus($aUrlId);
}

?>