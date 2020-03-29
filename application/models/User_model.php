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
	
	public function __construct()
	{   

		log_message('debug', __METHOD__.' '.__LINE__.' User_model class init:'."\n");
		parent::__construct(); //super class constructor
	}


	public function getUserInfo($userId= null){

       $sql = "SELECT * from Users where userId = ?";
       $query = $this->db->query($sql,array($userId));
       $result = $query->result_array();

       return $result;

	}

	public function getUsername($userId= null){

       $sql = "SELECT username from Users where userId = ?";
       $query = $this->db->query($sql,array($userId));
       $row = $query->row_array();

       if(isset($row))
          return $row;
       else
       	  return null;

	}

	public function verifyUserLogin($username, $upass){
    
       $sql = "SELECT count(*) as count from Users where username = ? and upass = ?";
       $query = $this->db->query($sql, array($username, $upass));
       $row = $query->row_array();

       $result = array();

       if($row['count'] > 0){

         $sql = "SELECT * from Users where username = ? and upass = ?";
         $query = $this->db->query($sql, array($username, $upass));
         $userInfo = $query->result_array();

         $result['flag']= 1;
         $result['userInfo']= array();

         foreach ($userInfo[0] as $key => $value) {
         	$result['userInfo'][$key]= $value;
         }
       
         return $result;
       }
       else{
       	 //log_message('debug', __METHOD__.' '.__LINE__.' Users Table Data : '.$count.'=> No Info'."\n");
       	 $result['flag']= 0;
         $result['userInfo'] = '';
       	 return $result;
       }

	}

	public function createUser($userInfo){

         $sql= "INSERT into Users (username, upass) values (?,?)";
         $query= $this->db->simple_query($sql, array($userInfo['username'], $userInfo['upass']));
         $result = array();

         if($query){
            $result['register']= true;
            $result['msg']= 'Registration Successful';
            return $result;
         }
         else{
         	$result['register']= false;
            $result['msg']= $this->db->error();
            return $result;
         }

	}


	public function verifyUsername($username){
         
       $sql = "SELECT count(*) from Users where username = ?";
       $query = $this->db->query($sql,array($username));
       $count = $query->num_rows();

       $result = array();

       if($count > 0){

         $result['found']= 1;
         $result['msg'] = 'Sorry!! '.$username.' already exist.';
         return $result;
       }
       else{
       	 $result['found']= 0;
         $result['msg'] = 'No user found !!';
       	 return $result;
       }

	}

	public function updateUsername($userInfo){

       $sql = "UPDATE  Users SET username = ? WHERE userId = ?";
       $query = $this->db->simply_query($sql,array($userInfo['username'], $userInfo['userId']));

       $result = array();

       if($query){

         $result['msg'] = 'Username updated Successfully!';
         return $result;
       }
       else{
         $result['msg'] = 'Update Unsuccessful!';
         $result['error'] = $this->db->error();
       	 return $result;
       }

	}



}


?>