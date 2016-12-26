<?php  if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mailer_model extends MY_Model
{
	public function __construct(){
        $this->table = '';
        $this->primary_key = '';
        $this->return_as = 'array';
        
        //disable default timestamps
        $this->timestamps = FALSE;
        
        parent::__construct();
	}
	
	public function get_all_emails($user){
	    $result = $this->db
                // ->select("*")
                ->select("messages.subject as subject")
                ->select("messages.body as body")
                ->select("messages.body as body")
                ->from("user_message_map")
	            ->join("messages", "user_message_map.message_id = messages.message_id", "inner")
	            ->join("users as author", "messages.author_id = author.user_id", "inner")
	            ->join("placeholders", "user_message_map.placeholder_id = placeholders.placeholder_id", "inner")
	            ->where("user_message_map.user_id", $user['user_id'])
	            ->get();
	            
        $return = false;
        $count = 0;
        foreach($result->result() as $row) {
            $return[$count] = $row;
            $count++;
        }
                    
        return $return;
	}
}