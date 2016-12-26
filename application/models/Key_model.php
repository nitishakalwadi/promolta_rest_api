<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Key_model extends MY_Model
{
	public function __construct(){
        $this->table = 'keys';
        $this->primary_key = 'id';
        $this->return_as = 'array';
        
        //disable default timestamps
        $this->timestamps = FALSE;
        
        parent::__construct();
	}
}