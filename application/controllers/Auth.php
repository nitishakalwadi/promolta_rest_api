<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        
        parent::__construct();
    }

	public function index(){
	    print "index";
	}
	
	public function login(){
	    echo json_encode($this->coreapp->auth->login());
	}
}
