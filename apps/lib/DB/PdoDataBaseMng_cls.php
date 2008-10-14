<?PHP
require_once('DataBase_cls.php');

/**
 * PdoDataBaseMng
 * PDO用いたデータベース管理クラス
 *
 * @access   public
 * @author  
 * @version  $Id: PdoDataBaseMng_cls.php, v 1.0 2008/04/03 14:00 Exp $
 * @copyright
 */

class PdoDataBaseMng extends DataBase{
	
    /**
     * PDOStatement オブジェクト（Prepare用）
     *
     * @var string
     */
	private $_oStmt = NULL;

    /**
     * PDOStatement オブジェクト（結果セット用）
     *
     * @var string
     */
	private $_oRs = NULL;
	
    /**
     * FECHT_MODE_1
     * カラム名で添字を付けた配列
     *
     * @var string
     */
	const FECHT_MODE_1 = 1;
	
    /**
     * FECHT_MODE_2
     * 0で始まるカラム番号で添字を付けた配列
     *
     * @var string
     */
	const FECHT_MODE_2 = 2;
	
    /**
     * FECHT_MODE_3
     * 結果セットに返された際のカラム名と 0 で始まるカラム番号で添字を付けた配列
     *
     * @var string
     */
	const FECHT_MODE_3 = 3;
	
    /**
     * FECHT_MODE_4
     * カラム名と同名のプロパティを有する 匿名のオブジェクト
     *
     * @var string
     */
	const FECHT_MODE_4 = 4;
	
	/**
	 * コンストラクタ
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version  $Id: PdoDataBaseMng_cls.php, v 1.0 2008/04/03 14:00 Exp $
	 */
	public function __construct($sDBName,$sUser=NULL,$sPass=NULL,$sHost=NULL,$sPort=NULL,$sDBType=NULL){
		if(!is_array($sDBName)){
			parent::__construct($sDBName,$sUser,$sPass,$sHost,$sPort,$sDBType);
		} else {
			parent::__construct($sDBName['DB_NAME'],
									$sDBName['DB_USER'],
									$sDBName['DB_PASS'],
									$sDBName['DB_HOST'],
									$sDBName['DB_PORT'],
									$sDBName['DB_KIND']);
		}
		
		return TRUE;
	}
	
	/**
	 * ConnectDataBase
	 * 単発データベースへ接続する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version  $Id: PdoDataBaseMng_cls.php, v 1.0 2008/04/03 14:00 Exp $
	 */
	 public function connectDataBase(){
		 $rtn = TRUE;
		 
		 // DSN作成
		 if($this->_createDSN()){
			 
			 
			 // DB接続
			 try {
				 $oDb = new PDO($this->_DSN);
				 
				 // エラーモードの指定
				 $oDb->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
				 
				 $this->_Con = $oDb;
			 } catch(PDOException $e) {
				$this->_Error[0] = $e->getCode();
				$this->_Error[1] = $e->getMessage();
				$rtn = FALSE;
			 }
		 }
		 
		 return $rtn;
	 }
	 
	/**
	 * disConnectDataBase
	 * 単発データベースを切断する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    public function disConnectDataBase(){
		$this->_Con = NULL;
		
		return TRUE;
	}
	
	/**
	 * _createDSN
	 * DSNを生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
	 public function _createDSN(){
		 $rtn = TRUE;

		 // PostgreSQL用DSN生成
		 if($this->getType() == DataBase::DB_POSTGRESQL){
			$this->_DSN = 'pgsql:';
			$this->_DSN .= 'host='.$this->getHost().' ';			// ホスト名
			$this->_DSN .= 'port='.$this->getPort().' ';			// ポート番号
			$this->_DSN .= 'dbname='.$this->getDBName().' ';		// データベース名
			$this->_DSN .= 'user='.$this->getUser().' ';		 	// ユーザ名
			$this->_DSN .= 'password='.$this->getPass();		// ユーザ名
		 // Oracle用DSN生成
		 } elseif ($this->_DBType == DataBase::DB_ORACLE){
			 $this->_DSN = 'oci:';
			 
		 } else {
			 $rtn = FALSE;
		 }		 
		 return $rtn;
	 }
	 
	/**
	 * executeSql
	 * SQLを実行する(結果セットを返さない物用)
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    public function executeSql($sql){
		$rtn = TRUE;
		
		$oDb = $this->_Con;
		
		try {
			$rtn = $oDb->exec($sql);

		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}
		
		return $rtn;
	}
	
	/**
	 * querySql
	 * SQLを実行する(結果セットを返す物用)
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    public function querySql($sql){
		$rtn = TRUE;
		
		$oDb = $this->_Con;
		
		try {
			$oRs = $oDb->query($sql);
			$this->_oRs = $oRs;
		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}

		return $rtn;
		
	}
	
	/**
	 * prepareSql
	 * SQL命令を準備する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    public function prepareSql($sql){
		$rtn = TRUE;
		
		$oDb = $this->_Con;

		try {
			$oStmt = $oDb->prepare($sql, array(PDO::ATTR_EMULATE_PREPARES => true));
			$this->_oStmt = $oStmt;
		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}

		return $rtn;
	}
	
	/**
	 * beginTrans
	 * トランザクションをスタートする
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
     public function beginTrans(){
		$oDb = $this->_Con;
		
		return $oDb->beginTransaction();
	}
	
	/**
	 * commitTrans
	 * トランザクションをコミットする
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
     public function commitTrans(){
		$rtn = TRUE;
		$oDb = $this->_Con;
		
		try {
			$rtn =$oDb->commit();
		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}

		return $rtn;
	 }

	/**
	 * rollbackTrans
	 * トランザクションをロールバックする
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
	 public function rollbackTrans(){
		$rtn = TRUE;
		$oDb = $this->_Con;
		
		try {
			$rtn =$oDb->rollBack();
		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}

		return $rtn;
	 }
	 
	/**
	 * escapeString
	 * サニタイジングを行う
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    public function escapeString($string){
		$oDb = $this->_Con;
		
		return $oDb->quote($string);
	}
	
	/**
	 * fetch
	 * fetch実行処理
	 * 
	 * @access public
	 * @param Integr $iMode Fetchモード
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */	
	public function fetch($iMode=FECHT_MODE_1){
		$rtn = FALSE;
		$iFetchMode = PDO::FETCH_ASSOC;
		
		// 
		if(is_null($this->_oRs)) return FALSE;
		
		$oStmt = $this->_oRs;
		
		switch($iMode){
			// カラム名で添字を付けた配列
			case FECHT_MODE_1:
				$iFetchMode = PDO::FETCH_ASSOC;
				break;
			// 0で始まるカラム番号で添字を付けた配列
			case FECHT_MODE_2:
				$iFetchMode = PDO::FETCH_NUM;
				break;
			// 結果セットに返された際のカラム名と 0 で始まるカラム番号で添字を付けた配列
			case FECHT_MODE_3:
				$iFetchMode = PDO::FETCH_BOTH;
				break;
			// カラム名と同名のプロパティを有する 匿名のオブジェクト
			case FECHT_MODE_4:
				$iFetchMode = PDO::FETCH_OBJ;
				break;
			default:
				return FALSE;
				break;
		}
		
		try {
			$rtn = $oStmt->fetch($iFetchMode);
		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}
		
		return $rtn;
	}
	
	/**
	 * fetch
	 * fetch実行処理
	 * 
	 * @access public
	 * @param Integr $iMode Fetchモード
	 * @author  M.Ozeki
	 * @version $Id:  PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */	
	public function fetchAll($iMode=PdoDataBaseMng::FECHT_MODE_1){
		$rtn = FALSE;
		$iFetchMode = PDO::FETCH_ASSOC;
		
		// 
		if(is_null($this->_oRs)) return FALSE;

		$oStmt = $this->_oRs;

		switch($iMode){
			// カラム名で添字を付けた配列
			case PdoDataBaseMng::FECHT_MODE_1:
				$iFetchMode = PDO::FETCH_ASSOC;
				break;
			// 0で始まるカラム番号で添字を付けた配列
			case PdoDataBaseMng::FECHT_MODE_2:
				$iFetchMode = PDO::FETCH_NUM;
				break;
			// 結果セットに返された際のカラム名と 0 で始まるカラム番号で添字を付けた配列
			case PdoDataBaseMng::FECHT_MODE_3:
				$iFetchMode = PDO::FETCH_BOTH;
				break;
			// カラム名と同名のプロパティを有する 匿名のオブジェクト
			case PdoDataBaseMng::FECHT_MODE_4:
				$iFetchMode = PDO::FETCH_OBJ;
				break;
			default:
				return FALSE;
				break;
		}

		return $oStmt->fetchAll($iFetchMode);
	}
	
	/**
	 * executeSelect
	 * select文実行処理
	 * 
	 * @access public
	 * @param str $sSql 実行SQL文
	 * @param Array $aBindParams プレースホルダー置換配列
	 * @author  M.Ozeki
	 * @version $Id: PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */	
	public function executeSelect($sSql,$aBindParams=NULL,$iBindMode=1){
		$oStmt = NULL;
		$oResultStmt = NULL;
		$rtn = TRUE;
		
		// プレイスホルダ使用するケース
		if(!is_null($aBindParams)){
			// SQL命令準備
			if(!$this->prepareSql($sSql)){
				return FALSE;
			}
			$oStmt = $this->_oStmt;
			
			try {
				// UnNamed バインド
				if($iBindMode == 1){
					// 実行
					$oResultStmt = $oStmt->execute($aBindParams);
				
				// Named バインド
				} else {
					// 値のセット
					foreach($aBindParams as $sKey => $sValue){
						${$sKey} = $sValue;
						$oStmt->bindParam(':'.$sKey,${$sKey});
					}
					
					// 実行
					$oResultStmt = $oStmt->execute();
				}
			} catch(PDOException $e) {
				$this->_Error[0] = $e->getCode();
				$this->_Error[1] = $e->getMessage();
				$this->_Error[2] = $e->getMessage();
				return FALSE;
			}
			
			$this->_oRs = $oStmt;
		// プレイスホルダ使用しないケース
		} else{
			$oResultStmt = $this->querySql($sSql);
			if(!$oResultStmt){
				$rtn = FALSE;
			} 
		}
		
		return $rtn;
	}
	
	/**
	 * executeInsert
	 * Insert文実行処理
	 * 
	 * @access public
	 * @param str     $sSql        実行SQL文
	 * @param Array   $aBindParams プレースホルダー置換配列
	 * @param Integer $iBindMode   バインドモード
	 * @return
	 * @author  M.Ozeki
	 * @version $Id: PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */	
	public function executeInsert($sSql,$aBindParams=NULL,$iBindMode=1){
		$oStmt = NULL;
		$oResultStmt = NULL;
		$rtn = TRUE;
	
		$oDb = $this->_Con;
		
		// プレイスホルダ使用するケース
		if(!is_null($aBindParams)){
			// SQL命令準備
			if(!$this->prepareSql($sSql)){
				return FALSE;
			}
			$oStmt = $this->_oStmt;
			
			try {	
				// UnNamed バインド
				if($iBindMode == 1){
					for($iCnt=0;$iCnt<count($aBindParams);$iCnt++){
						// 実行
						$rtn = $oStmt->execute($aBindParams[$iCnt]);					 
					}
				// Named バインド
				} else {
					// 値のセット
					foreach($aBindParams[0] as $sKey => $sValue){
						$oStmt->bindParam(':'.$sKey,${$sKey});
					}
					
					for($iCnt=0;$iCnt<count($aBindParams);$iCnt++){
						// 
						extract($aBindParams[$iCnt]);
						
						// 実行
						$rtn = $oStmt->execute();
						
						if($rtn === FALSE){
							$this->_Error = $oDb->errorInfo();
							$this->_Error[3] = 'lineNo：'.$iCnt;
							return FALSE;
						}
					}
					$rtn = $iCnt;
				}
			} catch(PDOException $e) {
				$this->_Error[0] = $e->getCode();
				$this->_Error[1] = $e->getMessage();
				$this->_Error[2] = $e->getMessage();
				$this->_Error[3] = 'lineNo：'.$iCnt;
				return FALSE;
			}
		// プレイスホルダ使用しないケース
		} else{
			$rtn = $this->executeSql($sSql);
		}
		
		return $rtn;
	}
	
	/**
	 * executeUpdate
	 * Update文実行処理
	 * 
	 * @access public
	 * @param str     $sSql        実行SQL文
	 * @param Array   $aBindParams プレースホルダー置換配列
	 * @param Integer $iBindMode   バインドモード
	 * @return
	 * @author  M.Ozeki
	 * @version $Id: PdoDataBaseMng_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */	
	public function executeUpdate($sSql,$aBindParams=NULL,$iBindMode=1){
		$oStmt = NULL;
		$oResultStmt = NULL;
		$rtn = TRUE;
		
		$oDb = $this->_Con;
		
		// プレイスホルダ使用するケース
		if(!is_null($aBindParams)){
			// SQL命令準備
			if(!$this->prepareSql($sSql)){
				return FALSE;
			}
			$oStmt = $this->_oStmt;
			
			try{
				// UnNamed バインド
				if($iBindMode == 1){
					// 実行
					$rtn = $oStmt->execute($aBindParams);
				// Named バインド
				} else {
					// 値のセット
					foreach($aBindParams as $sKey => $sValue){
						${$sKey} = $sValue;
						$oStmt->bindParam(':'.$sKey,${$sKey});
					}
	
					// 実行
					$rtn = $oStmt->execute();
				}
			} catch(PDOException $e) {
				$this->_Error[0] = $e->getCode();
				$this->_Error[1] = $e->getCode();
				$this->_Error[2] = $e->getMessage();
				return FALSE;
			}
		// プレイスホルダ使用しないケース
		} else{
			$rtn = $this->executeSql($sSql);
		}
		
		return $rtn;
	}
	
	/**
	 * executeDelete
	 * Delete文実行処理
	 * 
	 * @access public
	 * @param str     $sSql        実行SQL文
	 * @param Array   $aBindParams プレースホルダー置換配列
	 * @param Integer $iBindMode   バインドモード
	 * @return
	 * @author  M.Ozeki
	 * @version $Id: PdoDataBaseMng_cls.php,v 1.0 2008/05/12 M.Ozeki Exp $
	 */	
	public function executeDelete($sSql,$aBindParams=NULL,$iBindMode=1){
		$oStmt = NULL;
		$oResultStmt = NULL;
		$rtn = TRUE;
		
		$oDb = $this->_Con;
		
		// プレイスホルダ使用するケース
		if(!is_null($aBindParams)){
			// SQL命令準備
			if(!$this->prepareSql($sSql)){
				return FALSE;
			}
			$oStmt = $this->_oStmt;
			
			try{
				// UnNamed バインド
				if($iBindMode == 1){
					// 実行
					$rtn = $oStmt->execute($aBindParams);
				// Named バインド
				} else {
					// 値のセット
					foreach($aBindParams as $sKey => $sValue){
						${$sKey} = $sValue;
						$oStmt->bindParam(':'.$sKey,${$sKey});
					}

					// 実行
					$rtn = $oStmt->execute();
				}
			} catch(PDOException $e) {
				$this->_Error[0] = $e->getCode();
				$this->_Error[1] = $e->getCode();
				$this->_Error[2] = $e->getMessage();
				return FALSE;
			}
			
			$rtn = $oStmt->rowCount();
			
		// プレイスホルダ使用しないケース
		} else{
			$rtn = $this->executeSql($sSql);
		}
		
		
		return $rtn;
	}
	
	public function lastInsertId(){
		$rtn = TRUE;
		
		$oDb = $this->_Con;
		
		try {
			$rtn = $oDb->lastInsertId();

		} catch (PDOException $e){
			// エラー情報取得
			$rtn = FALSE;
			$this->_Error[0] = $e->getCode();
			$this->_Error[1] = $e->getCode();
			$this->_Error[2] = $e->getMessage();
		}
		
		return $rtn;
	}
}
?>