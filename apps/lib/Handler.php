<?php
class MyHandler {
    function MyHandler(){}
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

class SimpleUrlHandler extends MyHandler{
	var $_change_line_tag = array('div','li','p','blockquote','lh');
	var $_active_tag = array();
	var $_current_tag = array();
	var $_body_flag = false;

	var $_url = array();
	var $_url_subeject = array();
	
	var $_body_data = array();
	
    function SimpleUrlHandler(){ 	
    }
    
    function getUrl(){
    	return $this->_url;
    }
    function getSubeject(){
    	return $this->_url_subeject;
    }
    function getBodyData(){
    	return $this->_body_data;
    }
    function clearDataFiled(){
		$this->_active_tag = array();
		$this->_current_tag = array();
		$this->_body_flag = false;
	
		$this->_url = array();
		$this->_url_subeject = array();
		
		$this->_body_data = array();
    }
    
    function openHandler(& $parser,$name,$attrs) {
    	// 現在のアクティブなタグ種別を取得
    	array_push($this->_active_tag,$name);
      	    	
        if($name == 'a'){
    		$this->_url[] = $attrs['href'];
    	} elseif($name == 'img' && $this->_current_tag == 'a' ){
    		$this->_url_subeject[] = $attrs['alt'];   		
    	} elseif($name == 'body'){
    		$this->_body_flag = true;
    	} elseif($name == 'script'){
    		$this->_body_flag = false;
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
        if($this->_current_tag == 'a'){    	
        	$this->_url_subeject[] = $data;
    	} 
    	
    	if($this->_body_flag){
    		// タグによる改行
    		if(in_array($this->_current_tag,$this->_change_line_tag) !== FALSE){
    			$data = $data."\n";
    		}
    		// 句読点による改行
    		$data = str_replace("。","。\n",$data);
    		
    		$this->_body_data[] = mb_convert_kana($data, "ASKV","UTF-8");
    	}
	}
	
    function escapeHandler(& $parser,$data) {

    }
    
    function piHandler(& $parser,$target,$data) {
    }
    
    function jaspHandler(& $parser,$data) {
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