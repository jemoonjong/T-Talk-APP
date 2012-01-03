<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Error extends CI_Controller 
{

	function __construct(){
		parent::__construct();
		
        //로그인이 필요없는 모듈설정
        /*
		$this->allow = array(
            'test'
		);
	    */
	}
	
	
	/**
	 * Main::_remap()
	 * 
	 * @todo 프로세스가 존재하지 않으면 404페이지표현.
	 * @todo 페이지 디자인 요망..
	 * 
	 * @param sting $method			//호출 콘트롤러 명
	 * @param array $params			//파라메타값 
	 * @return
	 */
	public function _remap($method, $params = array()){
	    if (method_exists($this, $method))
	    {
	        return call_user_func_array(array($this, $method), $params);
	    }
	    
		$data = array();
        $data['result'] = 'error';
        $data['message'] = lang('error_404');	//페이지가 존재하지 않습니다.';

		$this->common_lib->show_message($data,$this->param);
	    //show_404();
	}

	
	
	public function index(){
		$data = array();
        $data['result'] = 'error';
        $data['message'] = lang('error_404');	//페이지가 존재하지 않습니다.';

		$this->common_lib->show_message($data,$this->param);
	}
	

	
	
}
