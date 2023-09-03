<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class Rate_model extends CI_Model {

	//instance variables
	
	public $rateId; 
	public $userId; 
	public $jokeId;
  public $rate; 


	public function __construct()
	{
		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.' Rate Model init:'."\n");
		//log_message('debug', __METHOD__.' '.__LINE__.' SERVER INFO : '.print_r($_SERVER, TRUE)."\n");
		$this->load->model('User_model', 'User');
	}


    /*
     * updateJokeRate; insert and update a new rate for a specified joke by the current user
     * @param $arr; array containing the joke, user and rate info
     *  
     */
    
    public function updateJokeRate($arr){
        

        $this->rateId= $arr['rateId'];
        $this->jokeId= "'".$arr['jokeId']."'";
        $this->userId= $arr['userId'];
        $this->rate=  $arr['rate'];

        $status = 0;
        $jokeId = $this->jokeId;
        $feedback = "Rate Insert/Update Unsuccessful!!";
        $num0fRatings = 0;
        $totalRateValue= 0;
        $avgRating = 0.0;

        //insert into Rating table;
        if(empty($this->rateId)){
          log_message('debug', __METHOD__.' '.__LINE__.' Joke Rate Insert option');
          $sql= "INSERT INTO  Rating (jokeId, userId, rate) VALUES (?,?,?)";
          $query = $this->db->query($sql, array($this->jokeId, $this->userId, $this->rate));
          $row = $query->result_array();
        }
        else{
          log_message('debug', __METHOD__.' '.__LINE__.' Joke Rate Update option');
          $sql= "UPDATE Rating  SET rate= ? WHERE rateId = ? and jokeId = ? and userId = ?";
          $query = $this->db->query($sql, array($this->rate, $this->rateId, $this->jokeId, $this->userId));
          $row= $query->result_array();
        } 

        if(!$row){
           log_message('debug', __METHOD__.' '.__LINE__.' Joke Rate Insert/Update Unsuccesful!'."\n".print_r($this->db->error(), TRUE)."\n");
              //die('Database Error !!');
          }
        else{

          //return required data for jokes table update
           $status = 1;
           $feedback = "Rate Insert/Update Succesful!!";

          if(empty($this->rateId))
            $this->rateId = $this->db->insert_id();  //return the newly generated rateId 

          $sql= "SELECT count(*) as num0fRatings, SUM(rate) as totalRateValue, jokeId from Rating 
                 WHERE rateId = ? and jokeId = ? GROUP BY jokeId"; 
          $query = $this->db->query($sql, array($this->rateId, $this->jokeId));
          $row2 = $query->result_array();

          if(!$row2){

             log_message('debug', __METHOD__.' '.__LINE__.' Other Calculated Ratings!'."\n".print_r($this->db->error(), TRUE)."\n");

          }else{

             $result = $row2;
             //log_message('debug', __METHOD__.' '.__LINE__.' Other Ratings : '.print_r($result, TRUE)."\n");

             if(isset($result)){
               $jokeId = $result['jokeId'];
               $num0fRatings = $result['num0fRatings'];
               $totalRateValue = $result['totalRateValue'];
               $avgRating = ($totalRateValue / $num0fRatings);
             }
          }
          
        }

        $return =array(
                 'insert'=>$status,
                 'jokeId'=>$jokeId, 
                 'numRate'=>$num0fRatings, 
                 'totalRate'=>$totalRateValue, 
                 'avgRate'=>$avgRating, 
                 'feedback'=>$feedback
               );

        return $return;
        
    }
    

}