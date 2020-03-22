<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {


	public function __construct(){

		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.'User Controller init:'."\n");

	}


	public function login()
	{
		log_message('debug', __METHOD__.' '.__LINE__.'User login attempt:'."\n");
		//$this->load->view('login');
	}
}


?>