<?php

// Include
require_once '../apps/conf_ini.php';
require_once 'apps/class/Process/CollectUrl/CollectUrlDao_cls.php';

// 初期化
$oDb = new CollectUrlDao($oDbConnMng);
$oParser = new HtmlParse();

//
for($iHierarchy=0;$iHierarchy<3;$iHierarchy++){
	echo "Hierarchy:".$iHierarchy."\n";
	
	// i階層のURLを取得する
	$aUrlHeader = $oDb->getUrlByHierarchy($iHierarchy);
	
	for($iCnt=0;$iCnt<count($aUrlHeader);$iCnt++){
		// アクセスする URL を指定
		echo $aUrlHeader[$iCnt]['url']."\n";
		
		// HTMLデータ取得
		$sHtml = getHtmlData($aUrlHeader[$iCnt]['url']);
		if(!$sHtml) continue;
		
		// UTF-8にエンコード
		$enc = mb_detect_encoding($sHtml);
		$sHtml = mb_convert_encoding($sHtml,"UTF-8",$enc);
		
		// 解析 
		$oParser->execHtmlParse($sHtml);
		$rtn = $oParser->getResult();
		
		if($rtn !== false){
			list($aSubject,$aUrl) = $rtn;

			foreach($aUrl as $iUrlNo => $sUrl){
				// URL存在チェック
				$id = $oDb->checkUrlExist($sUrl);
				if($id !== FALSE){
					// URLセット
					$id = $oDb->setUrl($sUrl);
				}				
				
				// 階層情報セット
				$oDb->setHierarchy($id,$aUrlHeader[$iCnt]['id'],$iHierarchy+1,$aSubject[$iUrlNo]);
			}
		}
	}
}
?>