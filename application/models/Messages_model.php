<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Messages_model extends MY_Model
{
	public function __construct(){
        $this->table = 'messages';
        $this->primary_key = 'message_id';
        $this->return_as = 'array';
        
        //relationships
        $this->has_one['author'] = array('User_model','user_id','author_id');
        $this->has_many['attachment'] = array('Attachment_message_map_model','message_id','message_id');
        
        //observers
        $this->before_create[] = 'created_at';
        
        parent::__construct();
	}
	
	protected function created_at($data){
        $data['created_at'] = date("Y-m-d H:i:s");
        return $data;
    }
}