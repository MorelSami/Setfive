<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class Comment_model extends CI_Model {

	//instance variables
	
	public $commentId;
	public $userId; 
	public $jokeId;
  public $comment;
  public $date;


	public function __construct()
	{
		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.' Comment Model init:'."\n");
		//log_message('debug', __METHOD__.' '.__LINE__.' SERVER INFO : '.print_r($_SERVER, TRUE)."\n");
		$this->load->model('User_model', 'User');
	}


    /*
     * createJokeComment; insert a comment for a specific joke by the current user
     * @param $arr; array containing the joke, user and comment info
     *  
     */
    
    public function createJokeComment($arr){
        

        $this->jokeId= "'".$arr['jokeId']."'";
        $this->userId= $arr['userId'];
        $this->comment = "'".$arr['comment']."'";

        $status = 0;
        $feedback = "Comment Insert Unsuccessful!!";


        //insert into Comments table;
          log_message('debug', __METHOD__.' '.__LINE__.' Joke Comment Insert init');

          $sql= "INSERT INTO  Comments (jokeId, userId, comment, date) VALUES (?,?,?, NOW())";
          $query= $this->db->query($sql, array($this->jokeId, $this->userId, $this->comment));
          $row = $query->row_array();

        if(!$row){
              
           log_message('debug', __METHOD__.' '.__LINE__.' Joke Comment Error !'."\n".print_r($this->db->error(), TRUE)."\n");
              //die('Database Error !!');
          }
        else{

           $status = 1;
           $feedback = "Comment Insert Successful!!";
          
        }

        $return =array(
                 'insert'=>$status, 
                 'feedback'=>$feedback
               );

        return $return;
        
    }


 /*
     * getJokeComments; fetch all comment saved for a specific joke for any user
     * @param $jokeId;  Id of the specific joke
     */
    
    public function getJokeComments($jokeId){
        

        $this->jokeId= "'".$jokeId."'";

        $sql= "SELECT * from Comments WHERE jokeId = ?";
        $query= $this->db->query($sql, array($this->jokeId));  
        $count = $query->num_rows();
        $result = $query->result_array();

        $comments_info = array();
        
        if ($count > 0) {

          $comments_info['found']= 1;
          $comments_info['response']= $result;
          $comments_info['numOfComments']= $count;

        } else {
           $comments_info['found']= 0;
           $comments_info['response']= null;
           $comments_info['numOfComments']= $count;

        }
        
        return $comments_info;
        
    }
    

     /*
     * getUserComments; fetch all comment saved for a specific joke by the current user
     * @param $jokeId;  Id of the specific joke
     * @param $userId;  Id of the current user
     */
    
    public function getUserComments($jokeId, $userId){
        

        $this->jokeId= "'".$arr['jokeId']."'";
        $this->userId= $arr['userId'];

        $sql= "SELECT * from Comments WHERE jokeId = ? and userId = ?";
        $query= $this->db->query($sql, array($this->jokeId, $this->userId));  
        $count = $query->num_rows();
        $result = $query->result_array();

        $comments_info = array();
        
        if ($count > 0) {

          $comments_info['found']= 1;
          $comments_info['response']= $result;
          $comments_info['msg']= $count." Record(s) Found !!";

        } else {
           $comments_info['found']= 0;
           $comments_info['msg']= $count." Record(s) Found !!";

        }
        
        return $comments_info;
        
    }
    

}