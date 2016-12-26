<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Email extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
    }
    
    public function index_post(){
        $this->response($this->coreapp->email->get_email_data());
    }
    
    public function send_post(){
        $this->response($this->coreapp->email->send());
    }
    
    public function save_draft_post(){
        $this->response($this->coreapp->email->save_draft());
    }
    
    public function fwd_post(){
        $this->response($this->coreapp->email->fwd_email());
    }
    
    public function reply_post(){
        $this->response($this->coreapp->email->reply_email());
    }
    
    public function trash_post(){
        $this->response($this->coreapp->email->trash_email());
    }
    
    public function attachment_post(){
        $this->response($this->coreapp->email->add_attachment());
    }
    
    public function all_get(){
        $this->response($this->coreapp->email->get_all_email_data());
    }
}
