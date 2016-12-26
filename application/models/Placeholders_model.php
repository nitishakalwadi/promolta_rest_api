<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Placeholders_model extends MY_Model
{
	public function __construct(){
        $this->table = 'placeholders';
        $this->primary_key = 'placeholder_id';
        $this->return_as = 'array';
        
        parent::__construct();
	}
}