<?php

class Coreapp_email extends CI_Driver {
    
    function __construct() {
	    
	}
	
	public function send(){
	    
	    $this->CI->form_validation->set_rules('to[]' , 'to email address' , 'required|valid_email'); 
        if ($this->CI->form_validation->run() == FALSE) { 
            $return['status'] = FALSE;
            $return['msg'] = $this->messages['invalid_to_address'];
            return $return;
        }
        
        $to                 = $this->CI->input->post("to");
	    $subject            = $this->CI->input->post("subject");
	    $body               = $this->CI->input->post("body");
	    $attachment_ids     = $this->CI->input->post("attachmentIds");
        
        $this->CI->load->model("user_model");
	    $this->CI->load->model("messages_model");
	    $this->CI->load->model("message_to_model");
	    $this->CI->load->model("threads_model");
	    $this->CI->load->model("thread_message_map_model");
	    $this->CI->load->model("attachment_model");
	    $this->CI->load->model("attachment_message_map_model");
	    
	    $to_user_data = $this->CI->user_model
                        ->where("email", $to)
	                    ->get_all();
	                    
	    $message = array(
            "subject" => $subject,
            "body" => $body,
            "author_id" => $this->user['user_id']
        );
        
        $this->CI->db->trans_start();
        $message_id = $this->CI->messages_model->insert($message);
        
        $message_to_insert_data = array();
        if(is_array($to)){
            foreach($to as $to_email){
                array_push($message_to_insert_data, ["message_id"=>$message_id, "to_email"=>$to_email]);
            }
            $this->CI->message_to_model->insert($message_to_insert_data);
        }
        
        $thread_insert_data = array();
        array_push($thread_insert_data, [ "belongs_to"=>$this->user["user_id"], "placeholder_id"=>2, "is_read"=>1 ]);
        if(is_array($to_user_data)){
            foreach($to_user_data as $to_data){
                array_push($thread_insert_data, [ "belongs_to"=>$to_data["user_id"], "placeholder_id"=>1 ]);
            }
            $threads = $this->CI->threads_model->insert($thread_insert_data);
        }
        
        $thread_message_map_insert_data = array();
        if(isset($threads) && is_array($threads)){
            foreach($threads as $thread_id){
                array_push($thread_message_map_insert_data, [ "thread_id"=>$thread_id, "message_id"=>$message_id ]);
            }
            $thread_message_map_data = $this->CI->thread_message_map_model->insert($thread_message_map_insert_data);
        }
        
        $attachment_data = $this->CI->attachment_model
                        ->where("attachment_id", $attachment_ids)
	                    ->get_all();
	    
	    $attachment_message_map_insert_data = array();
        if(is_array($attachment_data)){
            foreach($attachment_data as $attachment){
                array_push($attachment_message_map_insert_data, ["message_id"=>$message_id, "attachment_id"=>$attachment['attachment_id']]);
            }
            $attachment_message_map_data = $this->CI->attachment_message_map_model->insert($attachment_message_map_insert_data);
        }
        
        $this->CI->db->trans_complete();
        
        if ($this->CI->db->trans_status() === FALSE){
            $return["status"] = FALSE;
            $return["msg"] = $this->messages["email_send_error"];
        }
        else{
            $return["status"] = TRUE;
            $return["msg"] = $this->messages["email_send_success"];
        }
        
	    return $return;
	}
	
	public function get_all_email_data(){
	    $this->CI->load->model("threads_model");
	    $this->CI->load->model("thread_message_map_model");
	    $this->CI->load->model("attachment_model");
	    $this->CI->load->model("attachment_message_map_model");
	    $this->CI->load->model("messages_model");
	    $this->CI->load->model("placeholders_model");
	    
	    $threads = $this->CI->threads_model
	                ->where("belongs_to", $this->user["user_id"])
	                ->with_thread_message_map("order_inside:updated_at desc, created_at desc")
	                ->order_by("updated_at","desc")
	                ->order_by("created_at","desc")
	                ->get_all();
	    
	    $messages = FALSE;
	    $attachments = FALSE;
	    $message_id_arr = array();
	    if(is_array($threads)){
	        foreach($threads as $thread){
	            if(is_array($thread["thread_message_map"])){
	                foreach($thread["thread_message_map"] as $map){
                        array_push($message_id_arr, $map["message_id"]);
	                }
	            }
	        }
	        
	        $messages = $this->CI->messages_model
	                    ->where("message_id", $message_id_arr)
	                    ->with_author("fields:email")
	                    ->with_attachment()
	                    ->get_all();
	        
	        $attachment_id_arr = array();
	        if(is_array($messages)){
	            foreach($messages as $message){
	                if(is_array($message["attachment"])){
	                    foreach($message["attachment"] as $attachment){
	                        array_push($attachment_id_arr, $attachment["attachment_id"]);
	                    }
	                    $attachments = $this->CI->attachment_model
	                                ->where("attachment_id", $attachment_id_arr)
	                                ->get_all();
	                }
	            }
	        }
	    }
	    
	    $placeholders = $this->CI->placeholders_model->get_all();
	                
	    $return["status"] = TRUE;
	    $return["threads"] = $threads;
	    $return["messages"] = $messages;
	    $return["attachments"] = $attachments;
	    $return["placeholders"] = $placeholders;
	    
	    return $return;
	}
	
	public function get_email_data(){
	    $this->CI->form_validation->set_rules('message_id'   , 'Message ID'  , 'required'); 
        if ($this->CI->form_validation->run() == FALSE) { 
            $return['status'] = FALSE;
            $return['msg'] = $this->messages['message_id_required'];
            return $return;
        }
	    
	    $this->CI->load->model("messages_model");
	    
	    $message_id = $this->CI->input->post("message_id");
	    
	    $message_data = $this->CI->messages_model
                        ->where("message_id", $message_id)
                        ->with_author("fields:user_id,email")
                        ->with_attachment()
	                    ->get();
	                    
	    if($message_data){
            $return['status'] = TRUE;
            $return['data'] = $message_data;
        }
        else{
            $return['status'] = FALSE;
            $return['msg'] = $this->messages['generic_error'];
        }
        
        return $return;
	}
	
	public function add_attachment(){
	   // pr($_FILES);
	    $tmp_name = $_FILES["attachment"]["tmp_name"];
	    $filename = $_FILES["attachment"]["name"];
	    $type = $_FILES["attachment"]["type"];
	    
	    $upload_path = config_item('upload_path');
	    $upload_dir  = $upload_path['attachment_upload_dir'];
	    
	    $internal_filename = random_string('unique', 32);
	    $move_status = move_uploaded_file($tmp_name, $upload_dir."/".$internal_filename);
	    
	    if($move_status){
	        $this->CI->load->model("attachment_model");
	        $insert_data = array();
	        $insert_data['actual_file_name'] = $filename;
	        $insert_data['internal_file_name'] = $internal_filename;
	        $attachment_id = $this->CI->attachment_model->insert($insert_data);
	        if($attachment_id){
	            $return['status'] = TRUE;
	            $return['data']['attachment_id'] = $attachment_id;
	            $return['data']['attachment_name'] = $filename;
	            return $return;
	        }
	    }
	    
	    $return['status'] = FALSE;
	    $return['msg'] = $this->messages['generic_error'];
	    
	    return $return;
	}
	
	public function download_attachment(){
	    $attachment_id  = $this->CI->input->get("attachmentId");
	    
	    $this->CI->load->model("attachment_model");
	    
	    $upload_path = config_item('upload_path');
	    $upload_dir  = $upload_path['attachment_upload_dir'];
	    
	    $this->CI->load->model("attachment_model");
	    $attachment_data = $this->CI->attachment_model->where("attachment_id", $attachment_id)->get();
	    $actual_filename = $attachment_data['actual_file_name'];
	    $internal_filename = $attachment_data['internal_file_name'];
	    
	    $data = file_get_contents($upload_path."/".$internal_filename);
	    
	    force_download($actual_filename, $data);
	}
	
	public function fwd_email(){
	    $this->CI->form_validation->set_rules('to[]' , 'to email address' , 'required|valid_email'); 
        if ($this->CI->form_validation->run() == FALSE) { 
            $return['status'] = FALSE;
            $return['msg'] = $this->messages['invalid_to_address'];
            return $return;
        }
        
        $to                 = $this->CI->input->post("to");
	    $subject            = $this->CI->input->post("subject");
	    $body               = $this->CI->input->post("body");
	    $attachment_ids     = $this->CI->input->post("attachmentIds");
	    $post_thread_id          = $this->CI->input->post("threadId");
	    
	    $this->CI->load->model("user_model");
	    $this->CI->load->model("messages_model");
	    $this->CI->load->model("message_to_model");
	    $this->CI->load->model("threads_model");
	    $this->CI->load->model("thread_message_map_model");
	    $this->CI->load->model("attachment_model");
	    $this->CI->load->model("attachment_message_map_model");
	    
	    $to_user_data = $this->CI->user_model
                        ->where("email", $to)
	                    ->get_all();
	                    
	    $message = array(
            "subject" => $subject,
            "body" => $body,
            "author_id" => $this->user['user_id']
        );
        
        $this->CI->db->trans_start();
        $message_id = $this->CI->messages_model->insert($message);
        
        $message_to_insert_data = array();
        if(is_array($to)){
            foreach($to as $to_email){
                array_push($message_to_insert_data, ["message_id"=>$message_id, "to_email"=>$to_email]);
            }
            $this->CI->message_to_model->insert($message_to_insert_data);
        }
        
        $thread_insert_data = array();
        if(is_array($to_user_data)){
            foreach($to_user_data as $to_data){
                array_push($thread_insert_data, [ "belongs_to"=>$to_data["user_id"], "placeholder_id"=>1 ]);
            }
            $threads = $this->CI->threads_model->insert($thread_insert_data);
        }
        
        $thread_message_map_insert_data = array();
        array_push($thread_message_map_insert_data, [ "thread_id"=>$post_thread_id, "message_id"=>$message_id ]);
        if(isset($threads) && is_array($threads)){
            foreach($threads as $thread_id){
                array_push($thread_message_map_insert_data, [ "thread_id"=>$thread_id, "message_id"=>$message_id ]);
            }
            $thread_message_map_data = $this->CI->thread_message_map_model->insert($thread_message_map_insert_data);
        }
        
        $attachment_data = $this->CI->attachment_model
                        ->where("attachment_id", $attachment_ids)
	                    ->get_all();
	    
	    $attachment_message_map_insert_data = array();
        if(is_array($attachment_data)){
            foreach($attachment_data as $attachment){
                array_push($attachment_message_map_insert_data, ["message_id"=>$message_id, "attachment_id"=>$attachment['attachment_id']]);
            }
            $attachment_message_map_data = $this->CI->attachment_message_map_model->insert($attachment_message_map_insert_data);
        }
        
        $this->CI->db->trans_complete();
        
        if ($this->CI->db->trans_status() === FALSE){
            $return["status"] = FALSE;
            $return["msg"] = $this->messages["email_send_error"];
        }
        else{
            $return["status"] = TRUE;
            $return["msg"] = $this->messages["email_send_success"];
        }
        
	    return $return;
	}
	
	public function trash_email(){
	    $post_thread_id          = $this->CI->input->post("threadId");
	    
	    $this->CI->load->model("threads_model");
	    $update_status = $this->CI->threads_model
	                        ->where("thread_id", $post_thread_id)
	                        ->update(array("placeholder_id"=>4));
	                        
	    if($update_status){
	        $return['status'] = TRUE;
	    }
	    else{
	        $return['status'] = FALSE;
	        $return['msg'] = $this->messages['generic_error'];
	    }
	    
	    return $return;
	}
	
	public function reply_email(){
	    $body               = $this->CI->input->post("body");
	    $attachment_ids     = $this->CI->input->post("attachmentIds");
	    $post_message_id          = $this->CI->input->post("messageId");
	    
	    $this->CI->load->model("user_model");
	    $this->CI->load->model("messages_model");
	    $this->CI->load->model("message_to_model");
	    $this->CI->load->model("threads_model");
	    $this->CI->load->model("thread_message_map_model");
	    $this->CI->load->model("attachment_model");
	    $this->CI->load->model("attachment_message_map_model");
	    
	    $message = array(
            "body" => $body,
            "author_id" => $this->user['user_id']
        );
        
        $this->CI->db->trans_start();
        $message_id = $this->CI->messages_model->insert($message);
        
        $threads = $this->CI->thread_message_map_model
                    ->where("message_id", $post_message_id)
                    ->get_all();
        
        $thread_message_map_insert_data = array();
        if(isset($threads) && is_array($threads)){
            foreach($threads as $thread_id){
                array_push($thread_message_map_insert_data, [ "thread_id"=>$thread_id, "message_id"=>$message_id ]);
            }
            $thread_message_map_data = $this->CI->thread_message_map_model->insert($thread_message_map_insert_data);
        }
        
        $attachment_data = $this->CI->attachment_model
                        ->where("attachment_id", $attachment_ids)
	                    ->get_all();
	    
	    $attachment_message_map_insert_data = array();
        if(is_array($attachment_data)){
            foreach($attachment_data as $attachment){
                array_push($attachment_message_map_insert_data, ["message_id"=>$message_id, "attachment_id"=>$attachment['attachment_id']]);
            }
            $attachment_message_map_data = $this->CI->attachment_message_map_model->insert($attachment_message_map_insert_data);
        }
        
        $this->CI->db->trans_complete();
        
        if ($this->CI->db->trans_status() === FALSE){
            $return["status"] = FALSE;
            $return["msg"] = $this->messages["email_send_error"];
        }
        else{
            $return["status"] = TRUE;
            $return["msg"] = $this->messages["email_send_success"];
        }
        
	    return $return;
	}
}