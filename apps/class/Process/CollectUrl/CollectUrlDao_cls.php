<?php

class CollectUrlDao extends BaseDao {
	
	function CollectUrlDao($oConnMng){
		parent::BaseDao($oConnMng);
		$this->setDefaultDBNo(URL_DB);
	}
	
	// 階層ごとのURLリスト取得
	function getUrlByHierarchy($iHierarchy=0){
		$oConn = $this->getConn();
		
		$sSql ="select
					 h.id
					,u.url
					from t_hierarchy h
					inner join m_url u
					on h.url_id = u.url_id
					where h.hierarchy =".$iHierarchy;
		
		$oConn->executeSelect($sSql);
		$aUrlHeader = $oConn->fetchAll();
		
		return $aUrlHeader;
	}
	
	// URL存在チェック
	function checkUrlExist($sUrl){
		$oConn = $this->getConn();
		$sSql = "select
					 url_id
					from m_url
					where url = :url";

		if($oConn->executeSelect($sSql,array('url'=>$sUrl),2) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		$aUrlHeader = $oConn->fetchAll();
		
		if($aUrlHeader && count($aUrlHeader) != 0){
			return $aUrlHeader[0]['url_id'];
		} else {
			return FALSE;
		}
	}
	
	// URL Insert処理
	function setUrl($sUrl){
		$oConn = $this->getConn();
		$sSql =" INSERT INTO m_url(url) VALUES (:url)";

		$aParam = array();
		$aParam['url'] = $sUrl;
		
		if($oConn->executeInsert($sSql,array($aParam),2) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		$id = $this->getLastInsertUrlId();

		if($id === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		
		return $id;
	}
	
	function getLastInsertUrlId(){
		$oConn = $this->getConn();
		$sSql = "select
					 max(url_id)
					from m_url";

		if($oConn->executeSelect($sSql) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
		$aUrlHeader = $oConn->fetchAll();
		
		if($aUrlHeader && count($aUrlHeader) != 0){
			return $aUrlHeader[0]['max'];
		} else {
			return FALSE;
		}
	}
	
	// URL 階層情報Insert処理
	function setHierarchy($iUrlId,$iParentId,$iHierarchy,$sTitle){
		$oConn = $this->getConn();
		
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

		if($oConn->executeInsert($sSql,array($aParam),2) === FALSE){
			// エラー処理
			$this->_oConnMng->execErrorOccurs();
		}
	}
}

?>