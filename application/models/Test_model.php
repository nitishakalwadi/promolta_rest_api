<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Test_model extends MY_Model
{
	public function __construct()
	{
        $this->table = 'test';
        $this->primary_key = 'id';
        $this->return_as = 'array';
        
        //disable default timestamps
        $this->timestamps = FALSE;
        
        parent::__construct();
	}

    public function insert_dummy()
    {
        $insert_data = array(
            array(
                'user_id' => '1'
            )
        );
        $this->db->insert_batch($this->table, $insert_data);
    }
	

}