<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Threads_model extends MY_Model
{
	public function __construct(){
        $this->table = 'threads';
        $this->primary_key = 'thread_id';
        $this->return_as = 'array';
        
        //relationships
        $this->has_many['thread_message_map'] = array('Thread_message_map_model','thread_id','thread_id');
        
        parent::__construct();
	}
}