<?PHP
/**
 * PEAR::LOGクラスのインクルード
 *
 * @include PEAR::LOGクラスのインクルード
 */
include_once "apps/lib/PEAR/Log.php";

/**
 * LogMassage
 * 埋め込みログ管理クラス
 *
 * @access   public
 * @author  大関正洋
 * @version $Id: LogMassage_cls.php, v 1.0 2007/09/10 16:18 Exp $
 * @copyright
 */
class LogMassage {
    /**
     * PEAR::LOGクラスオブジェクト
     *
     * @var object
     */
	var $_oLogObj = null;
	
    /**
     * 区切り文字
     *
     * @var string
     */
	var $_sIndent = ',';

    /**
     * ログ出力レベル
     *
     * @var string
     */
	var $_iLevel = PEAR_LOG_DEBUG;

    /**
     * 実行ファイル名
     *
     * @var string
     */
	var $_sScript = ''; 
	
    /**
     * ログのフォーマット
     *
     * @var string
     */
	const LOG_FORMAT = '#IP##INDENT##CONTENT##INDENT##FILE_NAME#';
		
    /**
     * コンストラクタ
     *
     * @access   public
     * @author  M.Ozeki
     * @version $Id: IRJDisplayCommon_cls.php, v 1.0 2008/04/01 14:00 Exp $
     * @copyright
     */
     function LogMassage (){
		//
		$this->_iLevel = IRJ_LOG_LEVEL;
		
		// ログファイル保存先
		$sTitle = date("Ymd");
		$sFilePath = COMMON_DOCUMENT_ROOT.'log/'.$sTitle."_log";

		$aConf = array('append' => 1,  'timeFormat' => '%X %x', 'locking' => 1, 'mode' => 0777, 'eol' => "\n");
		
		$this->_oLogObj 
			= Log::factory('file',$sFilePath,$this->_sIndent,$aConf,$this->_iLevel);
	}
	
    /**
     * createMsg
     * 
     * 
     * @access  private
     * @author  M.Ozeki
     * @version $Id: IRJDisplayCommon_cls.php, v 1.0 2008/04/01 14:00 Exp $
     * @return 
     */
	function createMsg($sMsg){
		global $oAuth;

		// IP
		$sIp = $_SERVER['REMOTE_ADDR'];
		$sLogMsg = str_replace('#IP#',$sIp,LogMassage::LOG_FORMAT);
			
		// メッセージ
		$sLogMsg = str_replace('#CONTENT#',$this->_removeLineFeedCode($sMsg),$sLogMsg);

		// ファイルパス
		$sLogMsg = str_replace('#FILE_NAME#',$this->_sScript,$sLogMsg);
		
		// インデント
		$sLogMsg = str_replace('#INDENT#',$this->_sIndent,$sLogMsg);
		
		return $sLogMsg;
	}
	
	
    /**
     * writeDebugLogByMsg
     * 
     * 
     * @access  private
     * @author  M.Ozeki
     * @version $Id: IRJDisplayCommon_cls.php, v 1.0 2008/04/01 14:00 Exp $
     * @return 
     */
	function writeDebugLogByMsg($sMsg){
		$this->_oLogObj->debug($this->createMsg($sMsg));
		
	}
	
    /**
     * writeErrorLogByMsg
     * 
     * 
     * @access  private
     * @author  M.Ozeki
     * @version $Id: IRJDisplayCommon_cls.php, v 1.0 2008/04/01 14:00 Exp $
     * @return 
     */
	function writeErrorLogByMsg($sMsg){
		$this->_oLogObj->err($this->createMsg($sMsg));
		
	}
	
    /**
     * writeInfoLogByMsg
     * 
     * 
     * @access  private
     * @author  M.Ozeki
     * @version $Id: IRJDisplayCommon_cls.php, v 1.0 2008/04/01 14:00 Exp $
     * @return 
     */
	function writeInfoLogByMsg($sMsg){
		$this->_oLogObj->info($this->createMsg($sMsg));
		
	}
	
    /**
     * _removeLineFeedCode
     * ログメッセージ内の改行コードを無効化する
     *
     * @access   public
     * @author  大関正洋
     * @version $Id: LogMassage_cls.php, v 1.0 2007/09/10 16:18 Exp $
     * @copyright
     */
	function _removeLineFeedCode($sStr){
		$sTemp = $sStr;
		$sTemp = str_replace("\r\n","",$sTemp);
		$sTemp = str_replace("\r","",$sTemp);
		$sTemp = str_replace("\n","",$sTemp);
		
		return $sTemp;
	}
}

?>