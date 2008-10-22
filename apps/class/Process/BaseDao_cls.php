<?php
require_once('class/DB/DBConnMng_cls.php');

class BaseDao {
	var $_oConnMng = null;
	var $_iDb = 0;
	function BaseDao($oConnMng){
		$this->_oConnMng = $oConnMng;
	}
	
	function setDefaultDBNo($iNo){
		$this->_iDb = $iNo;		
	}
	
	function getConn($iNo=null){
		if(is_null($iNo)) $iNo = $this->_iDb;
		return $this->_oConn = $oConnMng->getConn($iNo);
	}
}
?>