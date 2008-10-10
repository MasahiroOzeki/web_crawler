<?php

require_once '../apps/conf_ini.php';

$oPDO = new PdoDataBaseMng($sDBName);
$oPDO->connectDataBase();

for($iHierarchy=0;$iHierarchy<3;$iHierarchy++){
	echo "Hierarchy:".$iHierarchy."\n";
	
	$oPDO->executeSelect('select * from t_url where hierarchy = '.$iHierarchy);
	$aUrlHeader = $oPDO->fetchAll();
	
	$oParser = new HtmlParse();
	
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
			list($aSubject,$aUrl,$aNonTagHtml) = $rtn;
			$sSql="INSERT INTO t_html(id, original_html, non_tag_html) VALUES (:id, :original_html, :non_tag_html)";
			
			$aParam['id'] = $aUrlHeader[$iCnt]['id'];
			$aParam['original_html'] = $sHtml;
			$aParam['non_tag_html'] = mb_convert_kana(implode("",$aNonTagHtml), "ASKV","UTF-8");

			$rtn=$oPDO->executeInsert($sSql,array($aParam),2);
			
			if(!$rtn){
				var_dump($oPDO->getError());
			}
		}
	}
}

?>