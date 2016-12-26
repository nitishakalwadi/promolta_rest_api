<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Thread_message_map_model extends MY_Model
{
	public function __construct(){
        $this->table = 'thread_message_map';
        $this->primary_key = 'thread_message_map_id';
        $this->return_as = 'array';
        
        //relationships
        $this->has_many['messages'] = array('Messages_model','message_id','message_id');
        
        parent::__construct();
	}
}