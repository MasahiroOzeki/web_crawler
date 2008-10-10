<?PHP

/**
 * SQLQuery
 * SQL生成クラス
 *
 * @access   public
 * @author  
 * @version  $Id: SQLQuery_cls.php, v 1.0 2008/04/08 14:00 Exp $
 * @copyright
 */
 
class SQLQuery{

	/**
	 * コンストラクタ
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version  $Id: SQLQuery_cls.php, v 1.0 2008/04/08 14:00 Exp $
	 */
    function __construct()
    {
    
    }
	
	/**
	 * GetSelectSQL
	 * Select文を生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version  $Id: SQLQuery_cls.php, v 1.0 2008/04/08 14:00 Exp $
     * @param   String          $tableName       テーブル名
     * @param   Array           $Data            カラム配列
     * @param   Array/String    $WhereData       検索条件
     * @param   Array/String    $SortData        ソート条件
     * @param   Array/String    $GroupData       グループ条件
     * @param   String          $sDistinct       Distinct指定
	 */
    function GetSelectSQL($tableName,$Data,$WhereData=NULL,$SortData=NULL, $GroupData=NULL, $sDistinct=NULL){  
        
		$sColums = '';						// カラム
		$sWhere  = " where ";				// 検索条件
		$sSort   = " order by ";			// ソート条件
		$sGroup  = " group by ";			// グループ条件
        		
		//-------------------------------
		// カラム
		//-------------------------------
		// カラム指定が配列の場合
		if(is_array($Data) && count($Data) != 0){
			$sColums = implode(",",$Data);
		// カラム指定が文字列の場合
		} elseif(is_string($Data)){
			$sColums = $Data;
		// 空白の場合
		} elseif(is_null($Data) || $Data == '' || count($Data) == 0) {
			$sColums = '*';
		}
		
		//-------------------------------
		// 検索条件
		//-------------------------------
        if (is_array($WhereData) && count($WhereData) != 0){
			$sWhere .= implode(" and ",$WhereData);
		} elseif(is_string($WhereData)){
			$sWhere .= $WhereData;
		} elseif(is_null($WhereData) || $WhereData == "" || count($WhereData) == 0) {
			$sWhere = '';
		}
		
		//-------------------------------
		// ソート条件
		//-------------------------------
        if (is_array($SortData) && count($SortData) != 0){
			$sSort .= implode(",",$SortData);
		} elseif(is_string($SortData)){
			$sSort .= $SortData;
		} elseif(is_null($SortData) || $SortData == "" || count($SortData) == 0) {
			$sSort = '';
		}
		
		//-------------------------------
		// グループ条件
		//-------------------------------
        if (is_array($GroupData) && count($GroupData) != 0){
			$sGroup .= implode(",",$GroupData);
		} elseif(is_string($GroupData)){
			$sGroup .= $GroupData;
		} elseif(is_null($GroupData) || $GroupData == "" || count($GroupData) == 0) {
			$sGroup = '';
		}

        return "select $sDistinct" . $sColums . " from " . $tableName . $sWhere . $sGroup . $sSort;
    }

	/**
	 * GetInsertSQL
	 * Select文を生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version  $Id: SQLQuery_cls.php, v 1.0 2008/04/08 14:00 Exp $
     * @param   String          $tableName       テーブル名
     * @param   Array           $Data            挿入データ配列
	 */
    function GetInsertSQL($tableName,$Data){
		
        $Data = $this->EscapeSQLQuery($Data);
    
        $strColums="";
        $strValues="";
		
        foreach ($Data as $key => $value){
            
            // into句
            if ($strColums!="") $strColums .= ",";
            $strColums .= "\"".$key."\"";
        
            // value句
            if ($strValues!="") $strValues .= ",";
            
            // PostGreSQL7.3.6以上でinteger型に空文字入力エラーを防ぐ
            // 2006/01/06 Update
            if ($value=="#__null__#") $strValues .= "null";
            else                      $strValues .= "'" . $value . "'";
        }
		
        return "insert into \"" . $tableName . "\"(" . $strColums .") values(" . $strValues .")";
    }
	
	/**
	 * GetInsertSQLForBind
	 * プレイスホルダ仕様のInsert文を生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: SQLQuery_cls.php, v 1.0 2008/04/25 14:00 Exp $
     * @param   String          $tableName       テーブル名
     * @param   Array           $Data            挿入データ配列
     * @return  array()
	 */
    function GetInsertSQLForBind($tableName,$Data){
        
        // SQL文生成
        $strColums="";
        $strValues="";
        
        $aBindParams = array();
		for($iCnt=0;$iCnt<count($Data);$iCnt++){
	        foreach ($Data[$iCnt] as $sKey => $sValue){
	            
	            // into句
	            if ($strColums!="") $strColums .= ",";
	            $strColums .= "\"".$sKey."\"";
	        
	            // value句
	            if ($strValues!="") $strValues .= ",";
	            
	            $strValues .= '?';
	
	            $aBindParams[$iCnt][] = $sValue;
	        }
		}
        $sSql = "insert into \"" .$tableName . "\"(" . $strColums .") values(" . $strValues .")";
        
        return array($sSql,$aBindParams);
    }
	
    // update文を作成する
    // 継承して$DataのkeyがtableNameに存在しているかのチェックを行うのがベター
    function GetUpdateSQL($tableName,$Data,$WhereSQL){
    
        $Data = $this->EscapeSQLQuery($Data);

        $strSet="";
        
        // PostGreSQL7.3.6以上でinteger型に空文字入力エラーを防ぐ
        // 2006/01/06 Update
        foreach ($Data as $key => $value){
            
            // set句
            if ($strSet!="") $strSet .= ",";
            //$strSet .= '"'.$key .'"'."='" . $value . "'";
            if ($value=="#__null__#") $strSet .= '"'.$key .'"'."= null";
            else                      $strSet .= '"'.$key .'"'."='" . $value . "'";;
        }
        
        // Where句があれば追加する
        if ($WhereSQL!="") {
	        if (is_array($WhereSQL) && count($WhereSQL) != 0){
				$sTempWhere .= implode(" and ",$WhereSQL);
			} elseif(is_string($WhereSQL)){
				$sTempWhere .= $WhereSQL;
			} elseif(is_null($WhereSQL) || $WhereSQL == "" || count($WhereSQL) == 0) {
				$sTempWhere = '';
			}
			
			$WhereSQL = " where " . $sTempWhere;
		}
        
        return 'update "' . $tableName . '" set ' . $strSet . $WhereSQL;
    }
	
	/**
	 * GetUpdateSQLForBind
	 * プレイスホルダ仕様のInsert文を生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: SQLQuery_cls.php, v 1.0 2008/04/25 14:00 Exp $
     * @param   String          $tableName       テーブル名
     * @param   Array           $Data            更新データ配列
     * @param   String/Array    $WhereSQL        検索句
     * @return  array()
	 */
    function GetUpdateSQLForBind($tableName,$Data,$WhereSQL){
         $strSet="";
		 
        // SET句
        $aBindParams = array();
        foreach ($Data as $sKey => $sValue){          
            
            if ($strSet!="") $strSet .= ",";
            
            $strSet .= '"'.$sKey .'"'."= ?";

            $aBindParams[] = $sValue;
        }
        
        // Where句があれば追加する
        $sTempWhere = '';
        if ($WhereSQL!="") {
	        if (is_array($WhereSQL) && count($WhereSQL) != 0){
		        foreach ($WhereSQL as $sKey => $sValue){          
					if ($sTempWhere != "") $sTempWhere .= " and ";
		            $sTempWhere .= $sKey.' = ?';
		            $aBindParams[] = $sValue;
		        }
			} elseif(is_string($WhereSQL)){
				$sTempWhere .= $WhereSQL;
			} elseif(is_null($WhereSQL) || $WhereSQL == "" || count($WhereSQL) == 0) {
				$sTempWhere = '';
			}
			
			$WhereSQL = " where " . $sTempWhere;
		}
        
        $sSql = 'update "' . $tableName . '" set ' . $strSet . $WhereSQL;
        
        return array($sSql,$aBindParams);
    }
	
    // delete文を作成する
    // 継承して$DataのkeyがtableNameに存在しているかのチェックを行うのがベター
    function GetDeleteSQL($tableName,$WhereSQL){
        
	    // Where句があれば追加する
	    if ($WhereSQL!="") {
	        if (is_array($WhereSQL) && count($WhereSQL) != 0){
				$sTempWhere .= implode(" and ",$WhereSQL);
			} elseif(is_string($WhereSQL)){
				$sTempWhere .= $WhereSQL;
			} elseif(is_null($WhereSQL) || $WhereSQL == "" || count($WhereSQL) == 0) {
				$sTempWhere = '';
			}
			
			$WhereSQL = " where " . $sTempWhere;
		}
		
        return 'delete from "' . $tableName . '" '.$WhereSQL;
    }
	
	/**
	 * GetDeleteSQLForBind
	 * プレイスホルダ仕様のDelete文を生成する
	 * 
	 * @access  public
	 * @author  M.Ozeki
	 * @version $Id: SQLQuery_cls.php, v 1.0 2008/04/25 14:00 Exp $
     * @param   String          $tableName       テーブル名
     * @param   Array           $Data            更新データ配列
     * @param   String/Array    $WhereSQL        検索句
     * @return  array()
	 */
    function GetDeleteSQLForBind($tableName,$WhereSQL){
         $strSet="";
		 
        // SET句
        $aBindParams = array();

        // Where句があれば追加する
        $sTempWhere = '';
        if ($WhereSQL!="") {
	        if (is_array($WhereSQL) && count($WhereSQL) != 0){
		        foreach ($WhereSQL as $sKey => $sValue){          
					if ($sTempWhere != "") $sTempWhere .= " and ";
		            $sTempWhere .= $sKey.' = ?';
		            $aBindParams[] = $sValue;
		        }
			} elseif(is_string($WhereSQL)){
				$sTempWhere .= $WhereSQL;
			} elseif(is_null($WhereSQL) || $WhereSQL == "" || count($WhereSQL) == 0) {
				$sTempWhere = '';
			}
			
			$WhereSQL = " where " . $sTempWhere;
		}
        
        $sSql = 'delete from "' . $tableName . '" '.$WhereSQL;
        
        return array($sSql,$aBindParams);
    }
    // SQLクエリー用に文字列をサニタイジングする
    function EscapeSQLQuery($Data){
        
        if (gettype($Data)=="array"){
            foreach ($Data as $Key => $Value) $Data[$Key] = $this->escapeString($Value);
        }
        else $Data = addslashes($Data);
        
        return($Data);
    }
    
    // サニタイジング用文字列
    // 継承して各DBに適したサニタイジング関数を設定するのがベター
    function escapeString($string)
    {
        return(addslashes($string));
    }

}

?>