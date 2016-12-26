<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Test extends REST_Controller {

    function __construct() {
        // Construct the parent class
        parent::__construct();
    }

    public function test_get() {
        $this->response([
            "get"=> "get"
        ]);
        // $this->response($this->coreapp->project->getProjectAssets());
    }
    
    public function test_post() {
        $this->response([
            "post"=> "post"
        ]);
    }
    
    public function test_delete() {
        $this->response([
            "delete"=> "delete"
        ]);
    }
    
    public function print_key_get(){
        print $this->input->server('HTTP_X_API_KEY');
    }
}
