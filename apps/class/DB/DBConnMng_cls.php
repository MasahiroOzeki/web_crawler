<?php
require_once('lib/DB/PdoDataBaseMng_cls.php');

class DBConnMng{
	var $_aDbInfo = array();
	var $_oDbConn = array();
	
	function DBConnMng($aDbInfo){
		// DB接続情報
		$this->_aDbInfo = $aDbInfo;
	}
	
	function getConn($iDbNo=1){
		if(!isset($this->_aDbInfo[$iDbNo])) return FALSE;
		
		if(!isset($this->_oDbConn[$iDbNo])){
			$oConn = new PdoDataBaseMng($this->_aDbInfo[$iDbNo]);
			if(!$oConn->connectDataBase()){
				var_dump($oConn->getError());
				return FALSE;		
			} 
			
			$this->_oDbConn[$iDbNo] = $oConn;
		} else {
			$oConn = $this->_oDbConn[$iDbNo];
		}
		
		return $oConn;
	}
	
	function execErrorOccurs(){
		var_dump($oConn->getError());
		exit();
	}
}
?>