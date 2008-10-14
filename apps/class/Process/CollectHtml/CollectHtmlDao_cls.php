<?php
class CollectHtmlDao {
	var $_oConnMng = null;
	var $_oConn = null;
	
	function CollectHtmlDao($oConnMng){
		$this->_oConnMng = $oConnMng;
		$this->_oConn = $oConnMng->getConn(1);
	}
	
	// HTMLデータ取得前のURLリスト取得
	function getUrl($iLimit=50){
		$oConn = $this->_oConn;
		
		$sSql ="select
					 url_id
					,url
					from m_url
					where status = 0
					offset 0 limit ".$iLimit;
		
		$oDb->executeSelect($sSql);
		$aUrlHeader = $oDb->fetchAll();
		
		return $aUrlHeader;
	}
	
	// HTMLデータ設定
	function setHtml($aHtml){
		if(count($aHtml) == 0) return FALSE;
		
		$oConn = $this->_oConn;
		
		$sSql ="INSERT
					 INTO m_html_org(url_id
						, html)
					VALUES (:url_id
						, :html)";
		
		if($oConn->executeInsert($sSql,$aHtml,2) !== FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		return TRUE;
	}
	
	// 
	function updateUrlStatus($aUrlId,$iStatus=1){
		if(count($aUrlId) == 0) return FALSE;
		
		$oConn = $this->_oConn;		
		
		$sSql ="UPDATE
					 m_url
					SET status = ".$iStatus."
					WHERE url_id in(".implode(',',$aUrlId).")";
	
		if($oConn->executeUpdate($sSql) !== FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		return TRUE;
	}
}
?>