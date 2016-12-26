<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Auth_rest extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
    }
    
    public function logout_post(){
        $this->response($this->coreapp->auth->logout());
    }
}
