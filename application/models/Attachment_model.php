<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Attachment_model extends MY_Model
{
	public function __construct(){
        $this->table = 'attachment';
        $this->primary_key = 'attachment_id';
        $this->return_as = 'array';
        
        parent::__construct();
	}
}