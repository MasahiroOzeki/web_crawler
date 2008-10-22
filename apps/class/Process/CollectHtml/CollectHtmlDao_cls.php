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
					where status = ".URL_STATUS_WAIT."
					offset 0 limit ".$iLimit."
					for update";
		
		if($oConn->executeSelect($sSql) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		// URLデータ取得
		$aUrlHeader = arrary();
		$aTargetId = arrary();
		while (($rtn = $oConn->fetch()) !== FALSE){
			$aUrlHeader[] = $rtn;
			$aTargetId[] = $rtn['url_id'];
		}
		
		// ステータス変更
		$this->updateUrlStatus($aTargetId,URL_STATUS_NOW);
		
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
	function updateUrlStatus($aUrlId,$iStatus=URL_STATUS_END){
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