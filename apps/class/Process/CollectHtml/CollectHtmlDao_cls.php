<?php
class CollectHtmlDao extends BaseDao  {
	
	function CollectHtmlDao($oConnMng){
		parent::BaseDao($oConnMng);
		$this->setDefaultDBNo(HTML_DATA_DB);
	}
	
	// HTMLデータ取得前のURLリスト取得
	function getUrl($iLimit=100){
		$oConn = $this->getConn(URL_DB);
		
		$sSql ="select
					 url_id
					,url
					from m_url
					where status = 0
					offset 0 limit ".$iLimit;
		
		if($oConn->executeSelect($sSql) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		$aUrlHeader = $oConn->fetchAll();
		
		return $aUrlHeader;
	}
	
	// HTMLデータ設定
	function setHtml($aHtml){
		if(count($aHtml) == 0) return FALSE;
		
		$oConn = $this->getConn();
		
		$sSql ="INSERT
					 INTO m_html_org(url_id
						, html)
					VALUES (:url_id
						, :html)";

		if($oConn->executeInsert($sSql,$aHtml,2) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		return TRUE;
	}
	
	// 
	function updateUrlStatus($aUrlId,$iStatus=1){
		if(count($aUrlId) == 0) return FALSE;
		
		$oConn = $this->getConn(URL_DB);
		
		$sSql ="UPDATE
					 m_url
					SET status = ".$iStatus."
					WHERE url_id in(".implode(',',$aUrlId).")";
	
		if($oConn->executeUpdate($sSql) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		return TRUE;
	}
}
?>