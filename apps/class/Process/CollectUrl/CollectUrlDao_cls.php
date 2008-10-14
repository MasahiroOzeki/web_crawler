<?php

class CollectUrlDao {
	var $_oConnMng = null;
	var $_oConn = null;
	
	function CollectUrlDao($oConnMng){
		$this->_oConnMng = $oConnMng;
		$this->_oConn = $oConnMng->getConn(1);
	}
	
	// 階層ごとのURLリスト取得
	function getUrlByHierarchy($iHierarchy=0){
		$oConn = $this->_oConn;
		
		$sSql ="select
					 h.id
					, u.url
					from t_hierarchy h
					inner join m_url u
					on h.url_id = u.url_id
					where h.hierarchy =".$iHierarchy;
		
		$oDb->executeSelect($sSql);
		$aUrlHeader = $oDb->fetchAll();
		
		return $aUrlHeader;
	}
	
	// URL存在チェック
	function checkUrlExist($sUrl){
		$oConn = $this->_oConn;
		$sSql = "select
					 id
					from m_url
					where url = :url";
		
		$oConn->executeSelect($sSql,array('url'=>$sUrl),2);
		$aUrlHeader = $oConn->fetchAll();
		
		if(count($aUrlHeader) != 0){
			return $aUrlHeader[0]['id'];
		} else {
			return FALSE;
		}
	}
	
	// URL Insert処理
	function setUrl($sUrl){
		$oConn = $this->_oConn;
		$sSql =" INSERT INTO m_url(url) VALUES (:url)";
		
		$aParam = array();
		$aParam['url'] = $sUrl;
		
		if($oConn->executeInsert($sSql,array($aParam),2) !== FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		$id = $oConn->lastInsertId();
		if($id !== FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		return $id;
	}
	
	// URL 階層情報Insert処理
	function setHierarchy($iUrlId,$iParentId,$iHierarchy,$sTitle){
		$oConn = $this->_oConn;
		
		$sSql ="INSERT
					 INTO t_hierarchy(url_id
						, hierarchy
						, link_title
						,parent_id)
					VALUES (:url_id
						, :hierarchy
						, :link_title
						,:parent_id)";
		
		$aParam = array();
		$aParam['url_id'] = $iUrlId;
		$aParam['hierarchy'] = $iHierarchy;
		$aParam['link_title'] = $sTitle;
		$aParam['parent_id'] = $iParentId;

		if($oConn->executeInsert($sSql,array($aParam),2) !== FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
	}
}

?>