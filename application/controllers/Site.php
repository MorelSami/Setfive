<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {


	public function __construct(){

		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.' Site Controller init:'."\n");
		//log_message('debug', __METHOD__.' '.__LINE__.' SERVER INFO : '.print_r($_SERVER, TRUE)."\n");
		$this->load->model('User_model', 'User');
		$this->load->model('Joke_model', 'Joke');

	}

	public function index()   //home page launch
	{
		log_message('debug', __METHOD__.' '.__LINE__.' Check user credetials:'."\n");
		$this->login();
	}

	public function login()   //home page launch
	{
		
		$data = array();
		$post = $this->input->post();
		$login = null;

		log_message('debug', __METHOD__.' '.__LINE__.' Posted Data : '.print_r($post, TRUE)."\n");
        
       if(isset($post) && !empty($post)){
          $login= $this->User->verifyUserLogin($post['username'], $post['upass']);
       }

       log_message('debug', __METHOD__.' '.__LINE__.' Login INFO : '.print_r($login['userInfo'], TRUE)."\n");


       if(isset($login)){
        if($login['flag'] != 0){
            
            $data['flag'] = 1;
        	$data['userInfo']= $this->buildArr($login['userInfo']);
        	$data['login_msg'] = 'Welcome to Dad Jokes';

        	log_message('debug', __METHOD__.' '.__LINE__.' User Logged In : '.print_r($data, TRUE)."\n");
             
            //load database with the latest jokes from the Host API
            $numPages = $this->Joke->fetchTotalPages();
            $this->Joke->fetchJokesApi($numPages);

            //get all jokes from database
            $db_result= $this->Joke->getJokes('', '');

            if($db_result['found'])
             $data['jokesInfo']= $db_result['response'];
            else
             $data['jokesInfo']= null;

        	$this->load->view('siteHeader', $data);
            $this->load->view('home', $data);
            $this->load->view('siteFooter');

        }
        else{
        	log_message('debug', __METHOD__.' '.__LINE__.' Redirect to login page:'."\n");
        	$data['flag']= 0;
        	$data['login_msg'] = 'Invalid Username or Password!';
            $this->load->view('siteHeader', $data);
            $this->load->view('login', $data);
            $this->load->view('siteFooter');
         }
       }
       else{
       	    $data['flag']= 0;
       	    $this->load->view('siteHeader', $data);
            $this->load->view('login', $data);
            $this->load->view('siteFooter');
       } 

		
	}

	public function searchJokes(){

       log_message('debug', __METHOD__.' '.__LINE__.' Search Jokes Service Initiated: '."\n");

       $data = array();
	   $post = $this->input->post();

	   log_message('debug', __METHOD__.' '.__LINE__.' Posted Data : '.print_r($post, TRUE)."\n");

	   $response = "<tr><td colspan='6' style='color:red; text-align:center;'> No Jokes Found! </td></tr>";   //default response

       if(isset($post) && !empty($post)){
          $result= $this->Joke->getJokes($post['option'], $post['value']);

          if($result['found'])
           	$response= $this->buildHtmlTable($result['response']);
           	
       }

       //log_message('debug', __METHOD__.' '.__LINE__.' Database Data : '.print_r($data['jokesInfo'], TRUE)."\n");

       echo json_encode($response);
       

	}

	public function viewJoke(){

       log_message('debug', __METHOD__.' '.__LINE__.' View Joke Service Initiated: '."\n");

       $data = array();
	   $post = $this->input->post();

	   log_message('debug', __METHOD__.' '.__LINE__.' Posted Data : '.print_r($post, TRUE)."\n");

	   $response = "No Joke(s) Found!";   //default response

       if(isset($post) && !empty($post)){
          $result= $this->Joke->getJokes($post['option'], $post['value']);

          if($result['found'])
           	$response= $result['response'];
           	
       }

       log_message('debug', __METHOD__.' '.__LINE__.' Database Data : '.print_r($response, TRUE)."\n");

       echo json_encode($response);
       

	}


    public function buildArr($inArr){
     
      $outArr = array();

      if(is_array($inArr)){
        foreach ($inArr as $key => $value) {
      	     $outArr[$key]= $value;
         }

         return $outArr;
      }
      else
      	return 'Array input required!';
      
    }

    public function buildHtmlTable($info= array()){

        $html= "";

        if(isset($info)){

            foreach ($info as $row){
               $html .="<tr style='padding:0.5%'>
                              <td>".$row['jokeId']."</td>
                              <td colspan='8'>".$row['joke']."</td>
                              <td>".$row['rate']."</td>
                              <td colspan='2'>".$row['AvgRating']."</td>
                              <td><a id='".$row['jokeId']."' title='view joke id:".$row['jokeId']."' onclick='viewJoke(this.id)' ><img src='".base_url()."application/views/img/view.jpeg' alt='view joke' width='25px' height='25px'></a></td>
                        </tr>";
                 }
          }
          else {
             $html.= "<tr><td colspan='6' style='color:red; text-align:center;'> No Jokes Found! </td></tr>";
         }

         return $html;

    }


}



?>