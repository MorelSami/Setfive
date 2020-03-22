<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {


	public function __construct(){

		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.'Site Controller init:'."\n");
		$this->load->model('User_model', 'User');

	}

	public function index()   //home page launch
	{
		log_message('debug', __METHOD__.' '.__LINE__.'Check user credetials:'."\n");
		$this->login();
	}

	public function login()   //home page launch
	{
		log_message('debug', __METHOD__.' '.__LINE__.'Redirect to login page:'."\n");
		$this->load->view('login');
	}

	public function welcome(){

       log_message('debug', __METHOD__.' '.__LINE__.'Redirect to home page:'."\n");
	   $this->load->view('home');

	}
}



?>