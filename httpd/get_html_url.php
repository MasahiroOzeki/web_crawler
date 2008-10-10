<?php

require_once '../apps/conf_ini.php';

/**
 * Web 取得
 */

$oPDO = new PdoDataBaseMng($sDBName);
$oPDO->connectDataBase();
var_dump($oPDO->getError());

$oParser = new HtmlParse();

for($iHierarchy=0;$iHierarchy<3;$iHierarchy++){
	echo "Hierarchy:".$iHierarchy."\n";
	
	$oPDO->executeSelect('select * from t_url where hierarchy = '.$iHierarchy);
	$aUrlHeader = $oPDO->fetchAll();
	
	for($iCnt=0;$iCnt<count($aUrlHeader);$iCnt++){
		// アクセスする URL を指定
		echo $aUrlHeader[$iCnt]['url']."\n";
		$sHtml = getHtmlData($aUrlHeader[$iCnt]['url']);
		if(!$sHtml) continue;
		
		//UTF-8にエンコード
		$enc = mb_detect_encoding($sHtml);
		$sHtml = mb_convert_encoding($sHtml,"UTF-8",$enc);
		
		//解析 
		$rtn = $oParser->execHtmlParse($sHtml);

		if($rtn !== false){
			list($aSubject,$aUrl) = $rtn;
			$sSql =" INSERT INTO t_url(url, title, hierarchy,p_id) VALUES (:url, :title, :hierarchy,:p_id)";
			foreach($aUrl as $iUrlNo => $sUrl){
				$aParam = array();
				$aParam['url'] = $sUrl;
				$aParam['hierarchy'] = $iHierarchy+1;
				$aParam['title'] = $aSubject[$iUrlNo];
				$aParam['p_id'] = $aUrlHeader[$iCnt]['id'];

				$rtn=$oPDO->executeInsert($sSql,array($aParam),2);
				
				if(!$rtn){
					var_dump($oPDO->getError());
				}
			}
		}
	}
}
?>