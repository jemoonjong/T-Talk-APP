<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Acl_hook{
	public $_DLANG = 'en';
	public $_LANG = array("en","ko","jp","zn");
	public $_FLANG = array(
		'en'	=>	'english',
		'ko'	=>	'korean',
		'jp'	=>	'japan',
		'zn'	=>	'china'
	);
	
    function checkPermission(){
		
        $CI =& get_instance();
        
		$CI->param = array(
			'result' 		=> 'jsonp',
			'jsoncallback'	=>	'jsoncallback',
			'ul'			=>	$this->_FLANG[$this->language()]
		);
		
        //파라메타값이 '/?a=1&b=2...'형태로 넘어온면
		if(isset($_SERVER['REDIRECT_QUERY_STRING'])) {
			$params = explode("&", $_SERVER["REDIRECT_QUERY_STRING"]);

			foreach($params as $p){
				if( ! empty($p)){
					@list($key, $value) = explode("=", $p);

					if(!is_null($value) && $value != ""){
						//만약 key값이 ul이면 ul의 값이 _LANG배열에 있는지 검사하여 없다면 _DLANG값으로 대처.
						if($key == 'ul'){
							if(in_array($value, $this->_LANG)) $CI->param[$key] = $this->_FLANG[$value];
							else $CI->param[$key] = $this->_FLANG[$this->_DLANG];
						}
						else $CI->param[$key] = $value;
					}
				}
			}
		}
		
		
		//POST로 넘어온다면 POST변수값이 우선.
		foreach($_POST as $key=>$value)
		{
			if(!is_null($value) && $value != ""){
				//만약 key값이 ul이면 ul의 값이 _LANG배열에 있는지 검사하여 없다면 _DLANG값으로 대처.
				if($key == 'ul'){
					if(in_array($value, $this->_LANG)) $CI->param[$key] = $this->_FLANG[$value];
					else $CI->param[$key] = $this->_FLANG[$this->_DLANG];
				}
				else $CI->param[$key] = $value;
			}
		}


		//실제 language폴더에 해당파일이 존재하는지 검사.. 존재하지 않으면 english폴더를 기본으로 가져옴.
		$lang_file = "./application/language/".$CI->param['ul']."/core_lang.php";
		if( ! read_file($lang_file)) $CI->param['ul'] = $this->_FLANG['en'];	
		
        //언어별 메세지 로드
		$CI->lang->load('core', $CI->param['ul']);

        
        if (isset($CI->allow) && (is_array($CI->allow) === FALSE OR in_array($CI->router->method, $CI->allow) === FALSE)) {
            if (1){
            	// 로그인을 했는지 판단
                // redirect url도 알아서... 
				if(!$CI->session->userdata('sess_id')){
					exit("hooks > acl_hook.php Login error.");
					redirect('/member/login');
					// . urlencode($CI->uri->ruri_string()));	
				}
            }   
        }
    }
    
    /**
     * Acl_hook::language()
     * 
     * 접속 사용자의 언어셋 구하기
     * 
     */
	function language(){
		$this->get_env_var('HTTP_ACCEPT_LANGUAGE');
		$this->get_env_var('HTTP_USER_AGENT');

		$_AL=strtolower($GLOBALS['HTTP_ACCEPT_LANGUAGE']);
		$_UA=strtolower($GLOBALS['HTTP_USER_AGENT']);

		foreach($this->_LANG as $K){
			if(strpos($_AL, $K)===0) return $K;
		}
		foreach($this->_LANG as $K){
			if(strpos($_AL, $K) !==false) return $K;
		} 

		return $this->_DLANG;
	}
	
	function get_env_var($Var){
		if(empty($GLOBALS[$Var])){
			$GLOBALS[$Var]=( ! empty($GLOBALS['_SERVER'][$Var]))?
			$GLOBALS['_SERVER'][$Var]: ( ! empty($GLOBALS['HTTP_SERVER_VARS'][$Var]))? $GLOBALS['HTTP_SERVER_VARS'][$Var]:'';
		}
	}
}


/* End of file Pear_hook.php */
/* Location: ./system/application/modules/hooks/Pear_hook.php */