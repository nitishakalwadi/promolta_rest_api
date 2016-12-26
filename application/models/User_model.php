<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model
{
	public function __construct(){
        $this->table = 'users';
        $this->primary_key = 'user_id';
        $this->return_as = 'array';
        
        //disable default timestamps
        $this->timestamps = FALSE;
        
        //relationships
        $this->has_many['messages'] = array('Messages_model','user_id','user_id');
        
        parent::__construct();
	}
}