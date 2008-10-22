<?php
class MyHandler {
    function MyHandler(){}
    function clearDataFiled(){}
    function openHandler(& $parser,$name,$attrs) {
        echo ( 'Open Tag Handler: '.$name.'<br />' );
        echo ( 'Attrs:<pre>' );
        print_r($attrs);
        echo ( '</pre>' );
    }
    function closeHandler(& $parser,$name) {
        echo ( 'Close Tag Handler: '.$name.'<br />' );
    }
    function dataHandler(& $parser,$data) {
        echo ( 'Data Handler: '.$data.'<br />' );
    }
    function escapeHandler(& $parser,$data) {
        echo ( 'Escape Handler: '.$data.'<br />' );
    }
    function piHandler(& $parser,$target,$data) {
        echo ( 'PI Handler: '.$target.' - '.$data.'<br />' );
    }
    function jaspHandler(& $parser,$data) {
        echo ( 'Jasp Handler: '.$data.'<br />' );
    }
}

class UrlGetHandler extends MyHandler{
	var $_active_tag = array();
	var $_current_tag = array();
	
	var $_t_subject = '';
	var $_url = array();
	var $_url_subeject = array();

	
    function UrlGetHandler(){ 	
    }
    
    function getUrl(){
    	return $this->_url;
    }
    
    function getSubeject(){
    	return $this->_url_subeject;
    }

    function clearDataFiled(){
		$this->_active_tag = array();
		$this->_current_tag = '';
		
		$this->_t_subject = '';
		$this->_url = array();
		$this->_url_subeject = array();
    }
    
    function openHandler(& $parser,$name,$attrs) {
    	// 現在のアクティブなタグ種別を取得
    	array_push($this->_active_tag,strtolower($name));
      	    	
        if(strtolower($name) == 'a'){
        	$this->_t_subject = '';
    		$this->_url[] = $attrs['href'];
    		
    	} elseif(strtolower($name) == 'img' && in_array('a',$this->_active_tag)){
    		if(isset($attrs['alt'])){
    			$this->_t_subject .= $attrs['alt'];
    		} else {
    			$this->_t_subject .= '';
    		}
    		
    	} 
    	    	
    	$this->_current_tag = strtolower($name);
    }
    
    function closeHandler(& $parser,$name) {
    	
    	if(strtolower($name) == 'a'){
    		$this->_url_subeject[] = $this->_t_subject;
    	}
    	
    	array_pop($this->_active_tag);
    	$this->_current_tag = '';
    }
    
    function dataHandler(&$parser,$data) {
        if($this->_current_tag == 'a' || in_array('a',$this->_active_tag)){
        	$this->_t_subject .= $data;
    	} 
   	}
	
    function escapeHandler(& $parser,$data) {
        if($this->_current_tag == 'a' || in_array('a',$this->_active_tag)){
        	$this->_t_subject .= $data;
    	} 
    }
    
    function piHandler(& $parser,$target,$data) {}
    
    function jaspHandler(& $parser,$data) {}
}

class RemovedTagHandler extends MyHandler{
	var $_active_tag = array();
	var $_current_tag = array();

	var $_change_line_tag = array('div','li','p','blockquote','lh','h1');
	var $_body_flag = false;
	var $_title = '';
	var $_body_data = array();
	var $_meta_data = array();
		
    function RemovedTagHandler(){ 	
    }
    
    function getBodyData(){
    	return $this->_body_data;
    }
    
    function getMetaData(){
    	return $this->$_meta_data;
    }
    
    function clearDataFiled(){
		$this->_active_tag = array();
		$this->_current_tag = '';
		
		$this->_body_flag = false;
		
		$this->_body_data = array();
    }
    
    function openHandler(& $parser,$name,$attrs) {
    	// 現在のアクティブなタグ種別を取得
    	array_push($this->_active_tag,$name);
      	    	
		if($name == 'body'){
    		$this->_body_flag = true;
    	} elseif($name == 'script'){
    		$this->_body_flag = false;
    	} elseif($this->_current_tag == 'META'){
    		$this->_meta_data[$attrs["name"] ] = $attrs["content"];
    	}
    	
    	$this->_current_tag = $name;
    }
    
    function closeHandler(& $parser,$name) {
    	array_pop($this->_active_tag);
    	$this->_current_tag = '';
    	
    	if($name == 'body'){
    		$this->_body_flag = false;
    	}elseif($name == 'script'){
    		$this->_body_flag = true;
    	}
    }
    
    function dataHandler(&$parser,$data) {   	
    	if($this->_body_flag){
    		// タグによる改行
    		if(in_array($this->_current_tag,$this->_change_line_tag) !== FALSE){
    			$data = $data."\n";
    		}
    		
    		// 
    		$data = $this->unhtmlentities($data);
    		
    		// 句読点による改行
    		$data = str_replace("。","。\n",$data);
    		
    		$this->_body_data[] = mb_convert_kana($data, "ASKV","UTF-8");
    	} else {
    		if($this->_current_tag == 'title'){
    			$this->_title = mb_convert_kana($data, "ASKV","UTF-8");
    		}    		
    	}
	}
	
    function escapeHandler(& $parser,$data) {

    }
    
    function piHandler(& $parser,$target,$data) {
    }
    
    function jaspHandler(& $parser,$data) {
    }
    
	function unhtmlentities($string)
	{
	    // 数値エンティティの置換
	    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
	    $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
	    // 文字エンティティの置換
	    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
	    $trans_tbl = array_flip($trans_tbl);
	    return strtr($string, $trans_tbl);
	}
}


class CategoryHandler extends MyHandler{
	var $_active_tag = array();
	var $_current_tag = array();
	
	var $_current_category = '';
	var $_current_category_url = '';
	var $_category = array();
	
	var $_url = array();
	var $_url_subeject = array();
	
    function MyHandler(){}
    function openHandler(& $parser,$name,$attrs) {
    	// 現在のアクティブなタグ種別を取得
    	array_push($this->_active_tag,$name);
    	$this->_current_tag = $name;
    	
    	// カテゴリー取得
    	if($this->_current_tag == 'img' && $attrs['alt'] == "カテゴリ："){
    		 $this->_current_category = 'ready';
    		 $this->_current_category_url = $attrs['href'];
    	}
    	
        if($this->_current_tag == 'a' && $this->_current_category != 'ready'){
    		$this->_url[$this->_current_category][] = $attrs['href'];
    	} else{
    		 $this->_current_category_url = $attrs['href'];   		
    	}

        echo ( 'Open Tag Handler: '.$name.'<br />' );
        echo ( 'Attrs:<pre>' );
        print_r($attrs);
        echo ( '</pre>' );

    }
    function closeHandler(& $parser,$name) {
    	
    	array_pop($this->_active_tag);

        echo ( 'Close Tag Handler: '.$name.'<br />' );

    }
    function dataHandler(& $parser,$data) {
        if($this->_current_tag == 'a'){
        	if($this->_current_category === 'ready'){
	        	$this->_category[] = $data;
	        	$this->_current_category = strval(count($this->_category)-1);
	        	
	        	$this->_url[$this->_current_category][] = $this->_current_category_url ;
        	}
        	
        	$this->_url_subeject[$this->_current_category][] = $data;
    	}
	}
	
    function escapeHandler(& $parser,$data) {

    }
    
    function piHandler(& $parser,$target,$data) {
    }
    
    function jaspHandler(& $parser,$data) {
    }
}
?>