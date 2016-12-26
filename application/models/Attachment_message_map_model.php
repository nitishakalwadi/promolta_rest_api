<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Attachment_message_map_model extends MY_Model
{
	public function __construct(){
        $this->table = 'attachment_message_map';
        $this->primary_key = 'attachment_message_map_id';
        $this->return_as = 'array';
        
        parent::__construct();
	}
}