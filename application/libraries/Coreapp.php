<?php

class Coreapp extends CI_Driver_Library {

	public $CI; // has to be public or dirver childs wont be able to access, if private then will have call again in childs
	public $user;
	public $messages;
	public $http_rest_key_name;

	function __construct(){
		$this->CI = & get_instance();
		
        $this->valid_drivers = array('auth', 'email'); 
        
        $this->set_messages();
        $this->set_http_rest_key_name();
        $this->set_user_from_key();
	}
	
	function set_messages(){
		$this->messages = config_item('messages');
	}
	
	function set_user_from_key(){
		$this->user = $this->_get_user_from_key();
	}
	
	function set_http_rest_key_name(){
		$this->http_rest_key_name = 'HTTP_' . strtoupper(str_replace('-', '_', config_item('rest_key_name')));
	}
	
	private function _get_user_from_key(){
        $key_name = 'HTTP_' . strtoupper(str_replace('-', '_', config_item('rest_key_name')));
        $key = $this->CI->input->server($key_name);
        
        if(!$key){
            return FALSE;
        }
        
        $this->CI->load->model('key_model');
        $this->CI->load->model('user_model');
        $key_data = $this->CI->key_model
                    ->where('key', $key)
                    ->get();
        
        if($key_data['user_id']){
            $user = $this->CI->user_model
                    ->where('user_id', $key_data['user_id'])
                    ->get();
                    
            return $user;
        }
        
        return FALSE;
   }
}