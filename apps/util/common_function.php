<?php
require_once('XML_HTMLSax.php');
require_once('util/Handler.php');

/**
 * Web 取得
 */
function getHtmlData($sUrl){
	if(is_null($sUrl) || $sUrl == '') return false;
	
	// URL をオープン
	@$fp = fopen($sUrl, 'r');
	
	if(!$fp) return false;
	
	// HTML を読み出す
	$sHtml = '';
	while (! feof($fp)) {
		$sHtml .= fread($fp, 1024) or die("READ");
	}
	
	// HTML のクローズ
	fclose($fp) or die("CLOSE");
	
	return $sHtml;
}

/**
 * 解析
 * 
 */
class HtmlParse {
	var $_handle = null;
	var $_parser = null;
	var $_mode = 1;
		
	function HtmlParse($iMode=1){
		// Instantiate the handler
		$this->_mode = $iMode;
		
		// URL取得
		if($this->_mode == 1){
			$this->_handler= new UrlGetHandler();
		// タグ除去
		} elseif($this->_mode == 2){
			$this->_handler= new SimpleUrlHandler();
		}

		
		//XML_HTMLSaxをインクルード
		$this->_parser =& new XML_HTMLSax();
		
		//自分のインスタンスをパーサーにセット
		$this->_parser->set_object($this->_handler);
		$this->_parser->set_option('XML_OPTION_TRIM_DATA_NODES');
		
		$this->_parser->set_element_handler('openHandler','closeHandler');
		$this->_parser->set_data_handler('dataHandler');
		$this->_parser->set_escape_handler('escapeHandler');
		$this->_parser->set_pi_handler('piHandler');
		$this->_parser->set_jasp_handler('jaspHandler');
	}
	
	function execHtmlParse($sHtml){
		$this->_handler->clearDataFiled();
		
		//パースする
		$this->_parser->parse($sHtml);
	}
	
	function getResult(){
		// URL取得
		if($this->_mode == 1){
			if(count($this->_handler->getUrl()) != 0){
				return array($this->_handler->getSubeject(),$this->_handler->getUrl());
			} else {
				return false;
			}
		// タグ除去
		} elseif($this->_mode == 2){
			$this->_handler= new SimpleUrlHandler();
		}
	}
}



?>