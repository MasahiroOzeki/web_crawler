<?php
require_once('lib/DB/PdoDataBaseMng_cls.php');

class DBConnMng{
	var $_aDbInfo = array();
	var $_oDbConn = array();
	var $_iDbNo = 1;
	
	function DBConnMng($aDbInfo){
		// DB接続情報
		$this->_aDbInfo = $aDbInfo;
	}
	
	function getConn($iDbNo=1){
		if(!isset($this->_aDbInfo[$iDbNo])) return FALSE;
		
		$this->_iDbNo = $iDbNo;
		
		if(!isset($this->_oDbConn[$this->_iDbNo])){
			$oConn = new PdoDataBaseMng($this->_aDbInfo[$this->_iDbNo]);
			if(!$oConn->connectDataBase()){
				var_dump($oConn->getError());
				return FALSE;
			} 
			
			$this->_oDbConn[$this->_iDbNo] = $oConn;
		} else {
			$oConn = $this->_oDbConn[$this->_iDbNo];
		}
		
		return $oConn;
	}
	
	function execErrorOccurs(){
		$oConn = $this->_oDbConn[$this->_iDbNo];
		var_dump($oConn->getError());
		exit();
	}
}
?>