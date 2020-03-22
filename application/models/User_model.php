<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class User_model  extends CI_Model
{
    
    //class instance variables
    public $userId;
    public $username;
    public $upass;
	
	function __construct(argument)
	{   

		log_message('debug', __METHOD__.' '.__LINE__.'User_model class init:'."\n");
		parent::__construct(); //super class constructor
	}

	




}


?>