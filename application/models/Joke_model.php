<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 
 */
class Joke_model extends CI_Model {

	//instance variables
	
	public $id; 
	public $userId; 
	public $joke; 
	public $totalRateValue; 
	public $numRate; 
	public $avgRate;

	public function __construct()
	{
		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.' Joke Controller init:'."\n");
		//log_message('debug', __METHOD__.' '.__LINE__.' SERVER INFO : '.print_r($_SERVER, TRUE)."\n");
		$this->load->model('User_model', 'User');
	}

   /*
     * fetch the total number of pages that have the searched jokes criteria; automatically runs each time the application start
     * to fetch new updates
     * @param $pageNum, the page number where to fetch the jokes
     *  
     */
    public function fetchTotalPages(){
        
        $url = 'https://icanhazdadjoke.com/search?term=hey';
        $user_agent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:74.0) Gecko/20100101 Firefox/74.0";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch,CURLOPT_USERAGENT,$user_agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $result= json_decode($data,TRUE);
        $res= array('Total Jokes'=>$result['total_jokes'], 'Total Pages'=> $result['total_pages']);
        
        if(!curl_errno($ch)){
            
            log_message('debug', __METHOD__.' '.__LINE__.' Total jokes & pages found based on the Search Term: '.print_r($res, TRUE)."\n");
            return $result['total_pages'];
            
        } else {
            log_message('error', __METHOD__.' '.__LINE__.' Curl error: '.curl_error($ch)."\n");
        }
        curl_close($ch);
       
    }



    /*
     * general_search; automatically runs each time the application start
     * to fetch new jokes from the host api
     * @param $pageNum, the page number where to fetch the jokes
     *  */
    public function fetchJokesApi($pageNum){

      $count= 1;

      while($count <=  $pageNum){
        
        //reload database with new fetched jokes from host api
        $url = 'https://icanhazdadjoke.com/search?term=hey&page='.$count.'&limit=30';
        $user_agent = "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:74.0) Gecko/20100101 Firefox/74.0";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch,CURLOPT_USERAGENT,$user_agent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/json"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $result_jokes= json_decode($data,TRUE);
        //log_message('debug', __METHOD__.' '.__LINE__.' Fetched Jokes: '.print_r($result_jokes, TRUE)."\n");
        
        if(!curl_errno($ch)){
            
            foreach ($result_jokes["results"] as $value)
            {
                $this->found = $this->checkJokes($value['id']); //checks database for existing joke
                if($this->found)
                    continue;
                else
                  $this->insertJokes($value);
            }
            
        } else {
            log_message('error', __METHOD__.' '.__LINE__.' Curl error: '.curl_error($ch)."\n");
        }
        curl_close($ch);

        $count ++;

     }//end while 
       
   }

        /*
     * insertJokes; insert every new joke into the database
     * from the host api
     * @param $arr; array containing the joke's info
     *  */
    
    public function insertJokes($arr= array()){
        
        $this->id=$arr['id'];
        $this->joke = str_replace("'","''",$arr['joke']);
        $this->avgRate = 0.0;
        $this->totalRateValue = 0;
        $this->numRate = 0;
        
        $sql= "INSERT INTO  Jokes (jokeId, joke, total_rate_value, num_of_ratings, AvgRating) VALUES (?,?,?,?,?)"; 
        $query= $this->db->simple_query($sql, array($this->id, $this->joke, $this->totalRateValue, $this->numRate, $this->avgRate));

        if(!$query){
              log_message('debug', __METHOD__.' '.__LINE__.' Joke Insert Unsuccesful! >'."\n".print_r($this->db->error(), TRUE)."\n");
              die('Datbase Error !!');
          }
        
    }
    
    /*
     * getJokes; fetch all joke from the database
     * @param $option; user input type selected for the joke search
     * @param $value; user input value used as the key for the joke search
     *  */
    
    public function getJokes($option, $value){
        
        switch ($option){
            case "Keyword":
                $sql = "select Jokes.jokeId, Jokes.userId, joke,rate,total_rate_value, num_of_ratings, AvgRating from Jokes left join Rating on Jokes.jokeId  = Rating.jokeId where joke like '%".$value."%'";
                break;
            case "Id":
                $sql = "select Jokes.jokeId, Jokes.userId, joke, rate, total_rate_value, num_of_ratings, AvgRating from Jokes left join Rating on Jokes.jokeId = Rating.jokeId where Jokes.jokeId = '".$value."'";
                break;
            case "Rating":
                $sql = "select Jokes.jokeId, Jokes.userId, joke, rate, total_rate_value, num_of_ratings, AvgRating from Jokes left join Rating on Jokes.jokeId = Rating.jokeId where rate= ".$value;
                break;
            case "AvgRate":
                $sql = "select Jokes.jokeId, Jokes.userId, joke, rate, total_rate_value, num_of_ratings, AvgRating from Jokes left join Rating on Jokes.jokeId = Rating.jokeId where AvgRating = ".$value;
                break;
            default:
                $sql = "select Jokes.jokeId, Jokes.userId, joke, rate, total_rate_value, num_of_ratings, AvgRating from Jokes left join Rating on Jokes.jokeId = Rating.jokeId";
              break;
        }
     
        $query= $this->db->query($sql);     
        $count = $query->num_rows();
        $result = $query->result_array();

        $jokes_info = array();
        
        if ($count > 0) {

          $jokes_info['found']= 1;
          $jokes_info['response']= $result;
          $jokes_info['msg']= $count." Record(s) Found !!";

        } else {
           $jokes_info['found']= 0;
           $jokes_info['msg']= $count." Record(s) Found !!";

        }
        
        return $jokes_info;
            
    }

    /*
     * checkJokes; cross check every joke fetch from the host api
     * across the database
     * @param $jokeId; joke id used to check for any existing joke in the database
     *  */
    public function checkJokes($jokeId){
        
        //initialize variables;
        
        $flag= false;
        $this->id = $jokeId;

        $sql = "select * from Jokes where jokeId = ?";
        $query = $this->db->query($sql, $this->id);
        $count= $query->num_rows();
        
        if ($count > 0) {
            $flag = true;
        }
        
        return $flag;
        
    }

}