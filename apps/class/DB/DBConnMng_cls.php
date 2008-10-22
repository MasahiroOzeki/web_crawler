<?php
require_once('lib/DB/PdoDataBaseMng_cls.php');

class DBConnMng{
	var $_aDbInfo = array();
	var $_oDbConn = array();
	var $_oLog = null;
	var $_iDbNo = 1;
	
	function DBConnMng($aDbInfo){
		//
		$this->_oLog = new LogMassage();
		
		// DB接続情報
		$this->_aDbInfo = $aDbInfo;
	}
	
	function getConn($iDbNo=1){
		if(!isset($this->_aDbInfo[$iDbNo])) return FALSE;
		
		$this->_iDbNo = $iDbNo;
		
		if(!isset($this->_oDbConn[$this->_iDbNo])){
			$oConn = new PdoDataBaseMng($this->_aDbInfo[$this->_iDbNo]);
			if(!$oConn->connectDataBase()){
				$aError = $oConn->getError();
				$this->_oLog->writeErrorLogByMsg($aError[0]);
				$this->_oLog->writeErrorLogByMsg($aError[1]);
				$this->_oLog->writeErrorLogByMsg($aError[2]);
				echo "connectDataBase Error!! ";
				exit();
			} 
			
			$this->_oDbConn[$this->_iDbNo] = $oConn;
		} else {
			$oConn = $this->_oDbConn[$this->_iDbNo];
		}
		
		return $oConn;
	}
	
	function execErrorOccurs(){
		$oConn = $this->_oDbConn[$this->_iDbNo];
		
		$aError = $oConn->getError();
		$this->_oLog->writeErrorLogByMsg($aError[0]);
		$this->_oLog->writeErrorLogByMsg($aError[1]);
		$this->_oLog->writeErrorLogByMsg($aError[2]);
		
		echo "SQL Error!! ";
		exit();
	}
}
?>