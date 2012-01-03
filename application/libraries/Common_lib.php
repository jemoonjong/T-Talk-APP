<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Common_lib {
    
    function __construct()
    {
        $this->CI =& get_instance();
    }


	/**
	 * Common::show_message($arr,$param_hook);
	 * 
	 * 전체적인 메세지 출력 모듈
	 * 
	 * @param array $arr			출력할 메세지내용
	 * @param array $param_hook		URL뒤에 넘어오는(/?result=xml....)모든 변수값 Array
	 * 
	 */
    public function show_message($arr, $param_hook)
    {
		$data = array();
		$data['data'] = array();
		$data['userParam'] = array();
		$data['section'] = array();
		
		//사용자 파라메타 값 입력하기
		foreach($param_hook as $key=>$val) $data['userParam'][$key] = trim($val);
		
		//섹션값 입력하기
		$data['section']['result'] = '';
		$data['section']['date'] = date("Y/m/d H:i:s",time());
		
		foreach($arr as $key=>$val){
			$data['section'][$key] = trim($val);
		}

    	if($param_hook['result'] == 'xml'){
    		
			$X = new XMLWriter;
			$X->openMemory();
			$X->setIndent(true);
			$X->startDocument('1.0', 'UTF-8');
			$X->startElement ('api');
			foreach($data as $key=>$val)
			{
				$X->startElement ($key);
					$this->_result_message($X,$val,'item');
				$X->endElement();
			}
			$X->endElement();
				
			Header("Content-type: text/xml; charset=UTF-8");
			print_r($X->flush());
			exit;
    	}
    	else if($param_hook['result'] == 'json')
    	{
    		Header("Content-type: text/json; charset=UTF-8");
    		echo json_encode($data);
    		exit;
    	}
    	else if($param_hook['result'] == 'array')
    	{

    		Header("Content-type: text/html; charset=UTF-8");
    		echo "<PRE>";
    		print_r($data);
    		exit;

    	}
        else	//jsonp
    	{
    		Header("Content-type: text/json; charset=UTF-8");

    		echo $param_hook['jsoncallback'].$this->arr2json($data);
    		exit;
    	}
    	
    }	
	
	
	
	
	
	
	/**
	 * Common::post_init()
	 * 
	 * @todo POST변수를 배열로 담아 될돌려줌.
	 * 
	 * @return array
	 */
	public function post_init()
    {
    	$post_data = array();
    	
        foreach($_POST as $key=>$val)
        {
            if(isset($key)){
            	if($key == 'result') $this->CI->param['result'] = $val; 
            	$post_data[$key] = urldecode(trim($val));
            	
            	//추후 로그파일 삭제할것
            	//$this->logMessage("[POST] ".$key."==>".$val);
            	
            }
        }

		foreach($_GET as $key=>$val)
        {
            if(isset($key)){ 
            	$post_data[$key] = urldecode(trim($val));
            	
            	//추후 로그파일 삭제할것
            	//$this->logMessage("[GET] ".$key."==>".$val); 
            }
        }
        
        
        return $post_data;
    }
    
    
    /**
     * Common::post_check()
     * 
     * @todo 파라메타값 검사
     * 
     * @param string $param
     * @param string $param_hook
     * 
     * @return void
     * 
     */
    function post_check($param, $param_hook)
    {
    	$error_key = array();

        foreach($param as $key=>$val)
        {
        	//만약 값이 array이면 key값이 변수명이됨.
        	if(is_array($val)){
        		$find_key = $key;
        	}else{
        	//만약 값이 array가 아니면 val값이 변수명이됨.	
        		$find_key = $val;
        	}
        	
            $result = $this->CI->input->post($find_key,'');
            
            //POST형태의 값이 없다면 GET으로 변수처리.
            if( ! $result ) $result = $this->CI->input->get($find_key,'');

			//만약 POST변수값이 없다면.
            if( ! $result)
            {
                $error_key[$find_key] = 'null';
            }
            //변수값은 있으나 그 값이 지정되어 있다면 그값이 맞는지 검사.
            else
            {
            	if(is_array($val)){
            		if( ! in_array($result, $val)){
            			$error_key[$find_key] = $result;
            		}
            	}
            }
        }
		//잘못된 변수가 있다면..
        if( ! empty($error_key)){
			$data = array();
        	$data['result'] = 'error';
        	$data['message'] = lang('error_param');	//'변수내 파라메타값 에러';
        	foreach($error_key as $key=>$val) $data[$key] = $val;
        	
        	$this->show_message($data,$param_hook);
        }
        
        return TRUE;
    }



    
    /**
     * Common::result_message()
     * 
     * API 결과값 리턴 모듈 (xml, json, jsonp)
     * 
     * @param array $arr
     * @param array $param_hook		파라메타데이터
     * @param string $element		XML엘레먼트 이름
     */
    public function result_message($arr, $param_hook, $element='item', $result = FALSE)
    {
    	if($param_hook['result'] == 'xml')
    	{
	    	$X = new XMLWriter;
			$X->openMemory();
			$X->setIndent(true);
			$X->startDocument('1.0', 'UTF-8');
			$X->startElement ('api');
			foreach($arr as $key=>$val)
			{
				$X->startElement ($key);
					$this->_result_message($X,$val,$element);
				$X->endElement();
			}
			$X->endElement();
				
			if($result === TRUE){
				return $X->flush();
			}
			else{
				Header("Content-type: text/xml; charset=UTF-8");
				print_r($X->flush());
				exit;
    		}
			
    	}
    	else if($param_hook['result'] == 'json')
    	{

			//이미지노드명 지정
    		$image_node = array('store_logo','store_gallery','user_image','coupon_phone_image','coupon_pos_image','board_image','signature');
    		foreach($arr['data'] as $key=>$val){

    			if(is_array($val))
    			{
    				foreach($val as $k=>$v)
    				{
    					//echo $k."---".$image_node."<BR>";
    					//이미지종류이면 이미지 전제 URL경로 구하기
						if($k != '0' && in_array($k, $image_node)){
    						$arr['data'][$key][$k] = $this->image_url($k, $v);
						}
    				}	
    			}
    			else{
    				//이미지종류이면 이미지 전제 URL경로 구하기
    				if($key != '0' && in_array($key, $image_node)){
						$arr['data'][$key] = $this->image_url($key, $val);
					}
    			}
    		}
			
    		if($result === TRUE){
    			return json_encode($arr);
    		}
    		else{
    			//echo"<PRE>";print_r($arr);exit;
    			Header("Content-type: text/json; charset=UTF-8");
    			print_r(json_encode($arr));
    			exit;
    		}
    	}
    	else if($param_hook['result'] == 'jsonp')
    	{
    	    //이미지노드명 지정
    		$image_node = array('store_logo','store_gallery','user_image','coupon_phone_image','coupon_pos_image','board_image','signature');

    		foreach($arr['data'] as $key=>$val)
    		{
    			if(is_array($val))
    			{
    				foreach($val as $k=>$v)
    				{
    					//이미지종류이면 이미지 전제 URL경로 구하기
						if($k != '0' && in_array($k, $image_node)){
    						$arr['data'][$key][$k] = $this->image_url($k, $v);
						}
    				}	
    			}
    			else{

    				//이미지종류이면 이미지 전제 URL경로 구하기
					if($key != '0' && in_array($key, $image_node)){
						$arr['data'][$key] = $this->image_url($key, $val);
					}
    			}
    		}

    		
    		if($result === TRUE){
    			return $param_hook['jsoncallback'].$this->arr2json($arr);
    		}
    		else{
    			Header("Content-type: text/json; charset=UTF-8");
    			print_r($param_hook['jsoncallback'].$this->arr2json($arr));
    			exit;
    		}
    		
    	}
    	else if($param_hook['result'] == 'array')
    	{
    	    //이미지노드명 지정
    		$image_node = array('store_logo','store_gallery','user_image','coupon_phone_image','coupon_pos_image','board_image','signature');
    		foreach($arr['data'] as $key=>$val){

    			if(is_array($val))
    			{
    				foreach($val as $k=>$v)
    				{
    					//이미지종류이면 이미지 전제 URL경로 구하기
						if($k != '0' && in_array($k, $image_node)){
    						$arr['data'][$key][$k] = $this->image_url($k, $v);
						}
    				}	
    			}
    			else{
    				//이미지종류이면 이미지 전제 URL경로 구하기
					if($key != '0' && in_array($key, $image_node)){
						$arr['data'][$key] = $this->image_url($key, $val);
					}
    			}
    		}
    		
    		
    		if($result === TRUE){
    			return $arr;
    		}
    		else{
    			Header("Content-type: text/html; charset=UTF-8");
    			echo "<PRE>";
    			print_r($arr);
    			exit;
    		}
    		
    	}
    	else return FALSE;
    }
    
    
    /**
     * Common::_result_message()
     * 
     * API결과값 배열변수 재귀호출 모듈
     * 
     * @param point $X				XML포인터변수
     * @param array $arr	
     * @param string $element		XML엘레먼트 이름
     */
    private function _result_message($X, $arr, $element){
    	
    	//이미지노드명 지정
    	$image_node = array('store_logo','store_gallery','user_image','coupon_phone_image','coupon_pos_image','board_image','signature');

    	foreach($arr as $key=>$val)
		{
			if( is_array($val)){
				if(is_int($key)) $key = $element;
				$X->startElement ($key);
					$this->_result_message($X, $val, $element);
				$X->endElement();
			}
			else
			{
				//이미지종류이면 이미지 전제 URL경로 구하기
				if($key != '0' && in_array($key, $image_node)){
					$val = $this->image_url($key, $val);
				}
				
				$X->startElement ($key);
					$X->text($val);
				$X->endElement();
			}
		}
    }
    
    
    /**
     * Common::arr2json()
     * 
     * JSONP 형태 만들기 
     * 
     * @param array $arr
     */
	public function arr2json($arr)
	{
		foreach($arr as $k=>$val) $json[] = '"'.$k.'"'.':'.$this->php2js($val); 
		
		if(!empty($json) && count($json) > 0) return '({'.implode(',', $json).'})'; 
		else return '""'; 
	}
	
	
	
		
	function php2js($val){
		
		if(is_array($val)) return $this->arr2json($val); 
		if(is_string($val)) return '"'.addslashes($val).'"'; 
		if(is_bool($val)) return 'Boolean('.(int) $val.')'; 
		if(is_null($val)) return '""'; 
		
		return $val;
	} 
    

    /**
     * Common::httpRequest()
     * 
     * @param string $url
     * @param string or array $data
     * @return void
     * 
     * HTTP통신 모듈
     * 
     */
    function httpRequest($url = '' , $data = '')
    {

        $this->CI->load->library('pearloader');
		$http_request = $this->CI->pearloader->load('HTTP','Request');
                
        $http_request->setMethod(HTTP_REQUEST_METHOD_POST);
        //POST변수 생성
        foreach ($data as $key => $val)
        {
            if (isset($key))
            {
                $http_request->addPostData($key,$val);
            }
        }

		$http_request->setURL($url);
		$http_request->sendRequest();
        $result = $http_request->getResponseBody();
        
        return $result;
    }
	
    
    /**
     * Common_lib::language()
     * 
     * 언어별 메세지 로딩
     * 
     * @param 언어파일명 		 $include_file
     * @param 랭귀지			 $ul
     */
    public function language($include_file,$ul)
    {
    	//언어별 메세지 로드
		return $this->CI->lang->load($include_file, $ul);
    }
    

	/**
	 * Common_lib::createDirectory()
	 * 
	 * @param string $dirName
	 * @return 해당 생성된 디렉토리
	 * 
	 * 디렉토리 생성하기
	 */
	function createDirectory($makeFolder = '')
	{
		$mFolder = explode("/",$makeFolder);
		$rootFolder = $_SERVER["DOCUMENT_ROOT"];
	
		$dir = '';
		for($i=0; $i<count($mFolder); $i++)
		{
			if($i == 0) $dir .= $rootFolder."/".$mFolder[$i];
			else $dir .= "/".$mFolder[$i];
			
			if(!is_dir($dir))
			{
				mkdir("$dir",0757);
				@chmod("$dir",0757);
			}
		}
		
		return $dir;
	}
	
	
	
	/**
	 * Common::logMessage()
	 * 
	 * @todo 로그메세지 파일로 저장
	 * 
	 * @param string $msg
	 * @return void
	 */
	function logMessage($msg = 'message')
	{
		$msgStr = "[".date("Y-m-d H:i:s",time())."] ".$msg."\r\n";
		$fileDir = $this->createDirectory('log');
		
		$fileName = $fileDir."/".date("Y-m-d",time()).".txt";

		if(file_exists($fileName)) {
			$fp = fopen($fileName, "a");
		} else {
			$fp = fopen($fileName, "w");
		}
		fwrite($fp, $msgStr);
		fclose($fp);
        
	}


	
	
	/**
	 * Common::random_code()
	 * 
	 * 원하는 갯수만큼의 랜덤한 고유키 만들기
	 * 
	 * @param unknown_type $len
	 * @param unknown_type $chars
	 */
	function random_code($len=10, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789')
	{
		$string = '';
		for ($i = 0; $i < $len; $i++){
			$pos = mt_rand(0, strlen($chars)-1);
			$string .= $chars{$pos};
		}
		return $string;
	}

	
	
	/**
	 * Common::image_url()
	 * 
	 * 전반적인 이미지 경로 구하기
	 * 
	 * @param 이미지타입 $img_type
	 * @param 이미지경로 $image
	 */
	public function image_url($img_type, $image)
	{
		//대표 이미지호스트 
		$image_host = $this->CI->config->item('image_host');
		shuffle($image_host);
		$imghost = $image_host[0];
							
		switch($img_type){
			CASE 'store_logo' :
				$img = '';
				
				if($unimg = @unserialize($image)){
					if( ! empty($unimg['applogo']) ) $image = $unimg['applogo'];
					else $image = '';
				}

				if( ! empty($image)){
					$img = $imghost.'/'.$image;
				}
				break;
			CASE 'store_gallery' :
				if($unimg = @unserialize($image)){
					$stroe_image = @unserialize($image);
				}
				else $stroe_image = $image;
				
				$img = '';
				if( !empty($stroe_image['gallery'])){
					foreach($stroe_image['gallery'] as $gallery){
						$img .= $imghost.'/images/stores'.$gallery.'|';
					}
					$img = substr($img,0,-1);
				}
				break;
			CASE 'user_image' :
				$img = '';
				if( ! empty($image)){
					$img = $imghost.'/'.$image;
				}
				break;
			CASE 'coupon_phone_image' :
				$img = '';
				if( ! empty($image)){
					$img = $imghost.'/images'.$image;
				}
				break;
			CASE 'coupon_pos_image' :
				$img = '';
				if( ! empty($image)){
					$img = $imghost.'/images'.$image;
				}
				break;
			CASE 'board_image' :
				$img = '';
				$talkimage = @unserialize($image);
				if( ! empty($talkimage)){
					for($i=0; $i<count($talkimage); $i++){
						$img .= $imghost.'/'.$talkimage[$i].'|';
					}
					$img = substr($img,0,-1);
				}
				break;
			CASE 'signature' :
				$img = '';
				if( ! empty($image)){
					$img = $imghost.'/'.$image;
				}
				break;
			DEFAULT :
				$img = '';
				break;
		}
		
		return $img;
		
	}


	/**
	 * Common_lib::image_file_trans()
	 * 
	 * 이미지서버로 파일 전송
	 * 
	 * @param	스트림파일변수명		$file_key
	 * @param	랜덤키				 $key
	 */
	public function image_file_trans($file_key,$key = '')
	{
		$data = NULL;
		
		//만약 key값이 없다면 랜덤 키 생성
		if( empty($key)) $key = $this->random_code();
		
		
		if( !empty($_FILES[$file_key])){
			//프로필이미지 폴더생성
			$folder = substr($key,0,1).'/'.substr($key,0,3).'/'.$key.'/profile';
			
			$tempFile = $_FILES[$file_key]['tmp_name'];
			$fileName = $_FILES[$file_key]['name'];
			$fileNameExt = strtolower(substr(strrchr($fileName, '.'), 1));

			//렌덤 파일명으로 지정
			$createFileName = $this->random_code().'.'.$fileNameExt;
			
			//이미지 서버로 이미지파일 전송
			$this->CI->load->library('pearloader');
			//이미지서버에 파일전송
			foreach($this->CI->config->item('dis_image_host') as $dis_img)
			{
				$http_request = $this->CI->pearloader->load('HTTP','Request');
				$http_request->setMethod(HTTP_REQUEST_METHOD_POST);
				
				$http_request->addFile('files', $tempFile);
				$http_request->addPostData('upload_type','user');
				$http_request->addPostData('dir',$folder);
				$http_request->addPostData('filename',$createFileName);
				
				$http_request->setURL($dis_img.'/file/file_upload');
				$http_request->sendRequest();
		        $rs = @json_decode($http_request->getResponseBody());
		        
		        if( empty($rs) || $rs->result != 'ok'){
		        	//$this->CI->common->logMessage(lang('image_member_error').' ['.$dis_img.']');
		        	$data = NULL;
		        }else{

		        	$data = $rs->file_path;
		        }
			}
			
		}
		
		return $data;
	}
	
	
	/**
	 * Common_lib::board_image_file_trans()
	 * 
	 * 글쓰기에서 이미지 첨부시 이미지서버로 전송하기
	 * 
	 * @param 사용자키값	 $user_key
	 */
	public function board_image_file_trans($user_key = '')
	{
		$data = array();
		//유저 키값이 없다면 새롭게 유저 키값 생성하기
		if( empty($user_key)) $user_key = $this->random_code();
		
		//프로필이미지 폴더생성
		$folder = substr($user_key,0,1).'/'.substr($user_key,0,3).'/'.$user_key.'/gallery';
		
		//이미지 배포호스트
		$dis_image_host = $this->CI->config->item('dis_image_host');
		
		//이미지 서버로 이미지파일 전송
		$this->CI->load->library('pearloader');
		
		if( ! empty($_FILES['images']['tmp_name']))
		{
			//배포 이미지 서버로 파일 전송
			$http_data = array();
			
			$tempFile = $_FILES['images']['tmp_name'];
			$fileName = $_FILES['images']['name'];
			$fileNameExt = strtolower(substr(strrchr($fileName, '.'), 1));
			//렌덤 파일명으로 지정
			$createFileName = $this->random_code().'.'.$fileNameExt;

			//이미지서버에 파일전송
			foreach($dis_image_host as $dis_img)
			{
				$http_request = $this->CI->pearloader->load('HTTP','Request');
				$http_request->setMethod(HTTP_REQUEST_METHOD_POST);
				
				$http_request->addFile('files', $tempFile);
				$http_request->addPostData('upload_type','user');
				$http_request->addPostData('dir',$folder);
				$http_request->addPostData('filename',$createFileName);
				
				$http_request->setURL($dis_img.'/file/file_upload');
				$http_request->sendRequest();
		        $rs = @json_decode($http_request->getResponseBody());
		        if( empty($rs) || $rs->result != 'ok'){
		        	$this->logMessage(lang('image_board_error').' ['.$dis_img.']');
		        }
		        else{
		        	$data[] = $rs->file_path;
		        }
			}
				
		}

		
		if( ! empty($data)){
			return serialize($data);
		}
		else{
			return '';
		}
	}
	
	

	

	
	/**
	 * Common_lib::image_file_remove()
	 * 
	 * 이미지 서버에 파일 삭제요청
	 * 구현해야함..............
	 * 
	 * @param unknown_type $user_key
	 */
	public function image_file_remove($user_key = ''){
		/*
		//해당 첨부 파일 삭제하기
		if( !empty($_FILES['user_image'])){
			//삭제할 폴더
			$http_data['dir'] = substr($user_key,0,1).'/'.substr($user_key,0,3).'/'.$user_key;
			$http_data['upload_type'] = 'user';
			foreach($this->config->item('image_host') as $dis_img)
			{
				$removeUrl = $dis_img.'/file/file_delete';
				$this->common->httpRequest($removeUrl,$http_data);
			}
		}
		*/
	}

	
	
    
   	/**
   	 * Common_lib::timer()
   	 * 
   	 * @return Timer
     * 
     * 두지점의 벤치마킹 타임측정
     * 시작점 과 끝점 지정
     * 
   	 */
   	function timer()
	{
		static $arr_timer;
		if(!isset($arr_timer))
		{
			$arr_timer = explode(" ", microtime());
		}
		else
		{
			$arr_timer2 = explode(" ", microtime());
			$result = ($arr_timer2[1] - $arr_timer[1]) + ($arr_timer2[0] - $arr_timer[0]);
			$result = sprintf("%.4f",$result);
			
			return $result;
		}
		
		return false;
	}
	
	
	/**
	 * Common_lib::transaction_error()
	 * 
	 * 데이타베이스 쿼리에러
	 * 
	 * @param unknown_type $msg
	 */
	public function transaction_error($msg = 'Database Transcation error.'){
		$data = array();
        $data['result'] = 'error';
        $data['message'] = $msg;

		$this->show_message($data,$this->CI->param);
		exit;
	}
}
