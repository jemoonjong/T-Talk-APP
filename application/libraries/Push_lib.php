<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Push_lib {
    
	public function __construct()
    {
        $this->CI =& get_instance();
    }


    /**
     * Push_lib::push_send_ok()
     * 
     * 결제성공 Push보내기
     * 
     * @param 사용자아이디	 $uid
     */
	public function push_send_ok($user_device, $user_device_token, $android_auth_token,$push_data, $message = '')
	{
		
		if($user_device == 'iphone'){
			$url = $this->CI->config->item('api_push_iphone_url');
			$data = array();
			$data['deviceToken'] = $user_device_token;
			$data['message'] = $message;
			foreach($push_data as $key=>$val) $data[$key] = $val;
			
			$result = $this->CI->common_lib->httpRequest($url, $data);
		}
		else if($user_device == 'android')
		{
			$ch = curl_init();   
			curl_setopt($ch, CURLOPT_URL, $this->CI->config->item('api_push_android_url'));
			
			$registration_id = $user_device_token;
			$collapse_key = 1;
			$auth = $android_auth_token;
			$msg = $message;
			
			$param = serialize($push_data);
			
			$data = 'registration_id='.$registration_id.'&collapse_key='.$collapse_key.'&data.param='.$param.'&data.msg='.urlencode($msg);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			
			$headers = array(
			    "Content-Type: application/x-www-form-urlencoded", 
			    "Content-Length: ".strlen($data), 
			    "Authorization: GoogleLogin auth=$auth" 
			);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	//결과값을 받을것인지 여부 ( '0' 받는다. '1' 받지않는다)
			$result = curl_exec($ch);
			curl_close($ch);
			
			
			//echo $result;
			//exit;
			
		}
	}
	
	
	/**
	 * Push_lib::push_send_error()
	 * 
	 * 결제실패 Push보내기
	 * 
	 * @param 사용자아이디	 $uid
	 */
	public function push_send_error($user_device, $user_device_token, $android_auth_token, $message = '')
	{
		if($user_device == 'iphone'){
			$url = $this->CI->config->item('api_push_iphone_url');
			$data = array();
			$data['deviceToken'] = $user_device_token;
			$data['message'] = $message;
			$data['code'] = 2;
			
			$this->CI->common_lib->httpRequest($url, $data);
		}
		else if($user_device == 'android')
		{
			$ch = curl_init();   
			curl_setopt($ch, CURLOPT_URL, "https://android.apis.google.com/c2dm/send");
			
			$registration_id = $user_device_token;
			$collapse_key = 1;
			$auth = $android_auth_token;
			$msg = $message;
			
			$push_data['code'] = 2;
			$param = serialize($push_data);
			
			$data = 'registration_id='.$registration_id.'&collapse_key='.$collapse_key.'&data.param='.$param.'&data.msg='.urlencode($msg);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			
			$headers = array(
			    "Content-Type: application/x-www-form-urlencoded", 
			    "Content-Length: ".strlen($data), 
			    "Authorization: GoogleLogin auth=$auth" 
			);
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	//결과값을 받을것인지 여부 ( '0' 받는다. '1' 받지않는다)
			$result = curl_exec($ch);
			curl_close($ch);
			
			//echo $result;
			
		}
	}
	
	
	
	
	/**
	 * Push_lib::push_login()
	 * 
	 * 타 기기로그인시 푸시메세지로 알려주기
	 * 
	 * @param 사용기기	 $user_device
	 * @param 디바이스토큰	 $user_device_token
	 * @param 안드로이드auth	 $android_auth_token
	 * @param 푸시데이터	 $push_data
	 * @param 메세지		 $message
	 */
	public function push_login($user_device, $user_device_token, $android_auth_token,$push_data, $message = '')
	{
		
		if($user_device == 'iphone'){
			$url = $this->CI->config->item('api_push_iphone_url');
			$data = array();
			$data['deviceToken'] = $user_device_token;
			$data['message'] = $message;
			$data['code'] = 9;
			foreach($push_data as $key=>$val) $data[$key] = $val;
			
			$result = $this->CI->common_lib->httpRequest($url, $data);
		}
		else if($user_device == 'android')
		{
			$ch = curl_init();   
			curl_setopt($ch, CURLOPT_URL, $this->CI->config->item('api_push_android_url'));
			
			$registration_id = $user_device_token;
			$collapse_key = 1;
			$auth = $android_auth_token;
			$msg = $message;
			$push_data['code'] = 9;
			
			$param = serialize($push_data);
			
			$data = 'registration_id='.$registration_id.'&collapse_key='.$collapse_key.'&data.param='.$param.'&data.msg='.urlencode($msg);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			
			$headers = array(
			    "Content-Type: application/x-www-form-urlencoded", 
			    "Content-Length: ".strlen($data), 
			    "Authorization: GoogleLogin auth=$auth" 
			);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	//결과값을 받을것인지 여부 ( '0' 받는다. '1' 받지않는다)
			$result = curl_exec($ch);
			curl_close($ch);
			
			
			//echo $result;
			//exit;
			
		}
	}
	
	
	
	
	
}


