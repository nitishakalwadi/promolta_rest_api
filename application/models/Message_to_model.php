<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Message_to_model extends MY_Model
{
	public function __construct(){
        $this->table = 'message_to';
        $this->primary_key = 'message_to_id';
        $this->return_as = 'array';
        
        parent::__construct();
	}
}