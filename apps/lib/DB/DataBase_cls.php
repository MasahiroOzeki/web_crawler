<?php
/**
 * DataBase
 * データベースアブストラクトクラス
 *
 * @access  public
 * @author M.Ozeki
 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
 **/
abstract class DataBase{
    /**
     * PostgreSQL定数
     *
     * @var string
     */
	const DB_POSTGRESQL = 'postgres';
	
    /**
     * Oracle定数
     *
     * @var string
     */
	const DB_ORACLE = 'oracle';
	
    /**
     * データベース名
     *
     * @var string
     */
    private $_DBName;
	
    /**
     * ユーザー名
     *
     * @var string
     */
    private $_User;
	
    /**
     * パスワード
     *
     * @var string
     */
    private $_Pass;
	
    /**
     * ホスト
     *
     * @var string
     */
    private $_Host;
	
    /**
     * データベースタイプ
     *
     * @var string
     */
    private $_DBType;
	
    /**
     * コネクションオブジェクト
     *
     * @var string
     */
    protected $_Con = NULL;
	
    /**
     * データソース名
     *
     * @var string
     */
    protected $_DSN;
	
    /**
     * ポート番号
     *
     * @var string
     */
    private $_Port = 5432;
	
    /**
     * エラーメッセージ
     *
     * @var string
     */
    protected $_Error;   
    
	/**
	 * コンストラクタ
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    public function __construct($sDBName,$sUser,$sPass,$sHost,$sPort,$sDBType)
    {
        $this->setDBName($sDBName);
        $this->setUser($sUser);
        $this->setPass($sPass);
        $this->setHost($sHost);
        $this->setPort($sPort);
		$this->setType($sDBType);
    }
	
	/**
	 * connectDataBase
	 * 単発データベースへ接続する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function connectDataBase();

	/**
	 * disConnectDataBase
	 * 単発データベースを切断する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function disConnectDataBase();
		
	/**
	 * _createDSN
	 * DSNを生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function _createDSN();
	
	/**
	 * executeSql
	 * SQLを実行する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function executeSql($sql);
	
	/**
	 * beginTrans
	 * トランザクションをスタートする
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function beginTrans();
	
	/**
	 * commitTrans
	 * トランザクションをコミットする
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function commitTrans();

	/**
	 * rollbackTrans
	 * トランザクションをロールバックする
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
	 abstract public function rollbackTrans();
	 
	/**
	 * escapeString
	 * サニタイジングを行う
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    abstract public function escapeString($string);
	
	/**
	 * setXXXX
	 * メンバー変数設定
	 * 
	 * @access  protected
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    protected function setDBName($data){ $this->_DBName = $data;}
    protected function setUser($data)  { $this->_User   = $data;}
    protected function setPass($data)  { $this->_Pass   = $data;}
    protected function setHost($data)  { $this->_Host   = $data;}
    protected function setCon($data)   { $this->_Con    = $data;}
    protected function setPort($data)  { $this->_Port   = $data;}
	protected function setType($data)  { $this->_DBType = $data;}
	
	/**
	 * getXXXX
	 * メンバー変数取得
	 * 
	 * @access  protected
	 * @author  M.Ozeki
	 * @version $Id: DataBase_cls.php,v 1.0 2008/04/02 M.Ozeki Exp $
	 */
    protected function getDBName(){ return $this->_DBName;}
    protected function getUser()  { return $this->_User;  }
    protected function getPass()  { return $this->_Pass;  }
    protected function getHost()  { return $this->_Host;  }
    protected function getCon()   { return $this->_Con;   }
    protected function getPort()  { return $this->_Port;  }
	protected function getType()  { return $this->_DBType;}
    public function getError() { return $this->_Error; }
}
?>