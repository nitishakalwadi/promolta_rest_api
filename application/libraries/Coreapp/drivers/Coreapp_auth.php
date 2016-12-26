<?php

class Coreapp_auth extends CI_Driver {

    private $logged_in = FALSE;
    private $key = FALSE;
    private $key_validity = FALSE;

	function __construct() {
	    
	}
	
	public function login() {
	    if($this->CI->input->server('REQUEST_METHOD') != 'POST'){
	        $return['status'] = FALSE;
	        $return['msg'] = $this->messages['invalid_method'];
	        return $return;
	    }
	    
	    $this->CI->form_validation->set_rules('email'       , 'Email ID'    , 'required'); 
        $this->CI->form_validation->set_rules('password'    , 'Password'    , 'required'); 
        if ($this->CI->form_validation->run() == FALSE) { 
            $return['status'] = FALSE;
            $return['msg'] = $this->messages['email_pass_empty'];
            return $return;
        }
	    
		$email      = $this->CI->input->post('email');
		$password   = $this->CI->input->post('password');
		
		$this->logged_in = $this->_login($email, $password);
	    
	    if($this->logged_in){
            $this->key = $this->_generate_key();
            $this->key_validity = $this->_get_key_validity();
            if($this->_insert_key( $this->key, ['user_id' => $this->user['user_id'], 'level' => 10], $this->key_validity )){
                $return['status'] = TRUE;
                $return[config_item('rest_key_name')] = $this->key;
                $return['valid_till'] = $this->key_validity;
                return $return;
            }
	    }
	    
	    $return['status'] = FALSE;
	    $return['msg'] = $this->messages['email_pass_invalid'];;
	    return $return;
	}
	
	public function logout(){
	    $key = $this->CI->input->server($this->http_rest_key_name);
	    $this->CI->load->model('key_model');
	    $status = $this->CI->key_model
	                ->where('key', $key)
	                ->delete();
	   
	   if($status){
	       $return['status'] = TRUE;
	   }
	   else{
	       $return['status'] = FALSE;
	       $return['msg'] = $this->messages['logout_failure'];
	   }
	   
	   return $return;
	}
	
	private function _insert_key($key, $data, $key_validity){
        $data[config_item('rest_key_column')] = $key;
        $data['date_created'] = function_exists('now') ? now() : time();
        $data[config_item('rest_key_validity_column')] = $key_validity;

        return $this->CI->db
            ->set($data)
            ->insert(config_item('rest_keys_table'));
    }
    
    private function _get_key_validity(){
        return (function_exists('now') ? now() : time()) + config_item('rest_key_validity');
    }
	
	private function _key_exists($key){
        return $this->CI->db
            ->where(config_item('rest_key_column'), $key)
            ->count_all_results(config_item('rest_keys_table')) > 0;
    }
	
	private function _generate_key(){
        // Generate a random salt
        $salt = base_convert(bin2hex($this->CI->security->get_random_bytes(64)), 16, 36);
        
        // If an error occurred, then fall back to the previous method
        if ($salt === FALSE){
            $salt = hash('sha256', time() . mt_rand());
        }
        
        $new_key = substr($salt, 0, config_item('rest_key_length'));
        while ($this->_key_exists($new_key));

        return $new_key;
    }
	
	
	private function _login($email, $password){
	    $this->CI->load->model('user_model');
        $user = $this->CI->user_model
	            ->where('email', $email)
	            ->get();
	            
        if($user && $user['password'] == $password){
            $this->user = $user;
            return TRUE;
        }
        else{
            return FALSE;
        }
	}
}