<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Controller {


	public function __construct(){

		parent::__construct();
		log_message('debug', __METHOD__.' '.__LINE__.' Site Controller init:'."\n");
		//log_message('debug', __METHOD__.' '.__LINE__.' SERVER INFO : '.print_r($_SERVER, TRUE)."\n");
		$this->load->model('User_model', 'User');
		$this->load->model('Joke_model', 'Joke');
		$this->load->model('Rate_model', 'Rate');
		$this->load->model('Comment_model', 'Comment');

		//log_message('debug', __METHOD__.' '.__LINE__.' SESSION DATA:'.print_r($_SESSION, TRUE)."\n");

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
          //log_message('debug', __METHOD__.' '.__LINE__.' Login INFO : '.print_r($login['userInfo'], TRUE)."\n");
       }

       $logged = $this->session->userdata('logged_in');

        if($logged){
            
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

        	if(isset($logged)){

        	  $data['flag']= 0;
        	  $data['login_msg'] = 'Invalid username or password!';

        	}
        	
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
       $jokeId = $this->input->post('value');
	   $post = $this->input->post();

	   log_message('debug', __METHOD__.' '.__LINE__.' Posted Data : '.print_r($post, TRUE)."\n");

	   
       $status = 0;
	   $msg = "No Joke(s) Found!";   //default response
	   $joke_info = "";
	   $comment_info = array();

       if(isset($post) && !empty($post)){

          $joke= $this->Joke->getJokes($post['option'], $post['value']);
          $comments = $this->Comment->getJokeComments($jokeId);

          if($joke['found']){
           	
           	$status = 1;
           	$msg = "Joke #".$jokeId." record found!"; 
           	$joke_info= $joke['response'];

           }

           $comment_info['count']= $comments['numOfComments'];
           $comment_info['records'] = $this->buildHtmlComments($comments['response']);
           	
       }

       $response = array("status"=>$status, "jokeInfo"=>$joke_info, "commentsInfo"=>$comment_info, "msg"=>$msg);
       echo json_encode($response);
       

	}

	public function updateJokeInfo(){
        
       log_message('debug', __METHOD__.' '.__LINE__.' Update Joke Info Service Initiated: '."\n");

       $data = array();
       $jokeId = $this->input->post('jokeId');
	   $post = $this->input->post();

	   log_message('debug', __METHOD__.' '.__LINE__.' Posted Data : '.print_r($post, TRUE)."\n");

       $status = 0;
       $db_result = '';
	   $msg = "Joke #".$jokeId." Info Update Unsuccessful!";   //default response

       if(isset($post) && !empty($post)){
          $result= $this->Rate->updateJokeRate($post); //update rate for that Joke

          log_message('debug', __METHOD__.' '.__LINE__.' updated Joke Rate result : '.print_r($result, TRUE)."\n");

          if($result['insert'])
           	$updated = $this->Joke->updateJoke($result); //update all ratings info for that Joke
          else
          	$updated = false;

          if($updated){
            $status = 1;
            $msg = "Joke #".$jokeId." Info Update Successful!";

            $jokes= $this->Joke->getJokes('','');

           if($jokes['found'])
           	 $db_result= $this->buildHtmlTable($jokes['response']);

          }

           	
       }
       
       $response = array('status'=>$status, 'records'=> $db_result, 'msg'=>$msg);
       echo json_encode($response);

	}


	public function commentJoke(){

      log_message('debug', __METHOD__.' '.__LINE__.' Comment Joke Service Initiated: '."\n");

       $data = array();
       $jokeId = $this->input->post('jokeId');
       $userId = $this->input->post('userId');
	   $post = $this->input->post();

	   log_message('debug', __METHOD__.' '.__LINE__.' Posted Data : '.print_r($post, TRUE)."\n");

       $status = 0;
       $db_result = '';
       $numOfComments= 0;
	   $msg = "Joke #".$jokeId." Comment Unsuccessful!";   //default response

       if(isset($post) && !empty($post)){
          
          $result = $this->Comment->createJokeComment($post);

          if($result['insert']){

          	 $comments=$this->Comment->getJokeComments($jokeId);
          	 $status = $comments['found'];
          	 $db_result= $this->buildHtmlComments($comments['response']);
          	 $numOfComments= $comments['numOfComments'];
          	 $msg = "Joke #".$jokeId." Comment Successful!"; 

          }
                   	
       }
       
       $response = array('status'=>$status, 'records'=> $db_result, 'numOfComments'=>$numOfComments, 'msg'=>$msg);
       echo json_encode($response);

	}

	public function logout(){

       log_message('debug', __METHOD__.' '.__LINE__.' Log Out Service Initiated: '."\n");

       log_message('debug', __METHOD__.' '.__LINE__.' Redirect to login page:'."\n");

       $this->session->sess_destroy();

        $this->load->view('siteHeader');
        $this->load->view('login');
        $this->load->view('siteFooter');
       

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


    public function buildHtmlComments($info = array()){

        $html = "";

        if(isset($info)){

          foreach ($info as $row) {
        	$html .= "<div id='".$row['userId']."' class='row user'>
        	           <div id='comment-".$row['commentId']."' class='col comment-box'>
	  	    			<label><img src='".base_url()."application/views/img/user_profile.png' alt='profile icon' width='50px'      height='50px'>". $this->session->userdata('username')." 
	  	    			</label></br>
	  	    			<p class='comment' name='user_".$row['userId']."comment_".$row['commentId']."'>".$row['comment']."</p>
	  	    		 </div><!--comment-box-->
	  	    	    </div><!--user-->";
        	}

        	$html .= "<div id='user-".$this->session->userdata('userId')."' class='row user'>
        	           <div id='comment-0' class='col comment-box'>
	  	    			<label><img src='".base_url()."application/views/img/user_profile.png' alt='profile icon' width='50px'      height='50px'>". $this->session->userdata('username')." 
	  	    			</label></br>
	  	    			<input type='text' id='comment' name='comment' /> <button id='saveComment' type='button'> comment </button></br></br>
	  	    		  </div><!--comment-box-->
	  	    		</div><!--user-->";

        }
        else{
        	$html .= "<div id='user-".$this->session->userdata('userId')."' class='row user'>
        	           <div id='comment-0' class='col comment-box'>
	  	    			<label><img src='".base_url()."application/views/img/user_profile.png' alt='profile icon' width='50px'      height='50px'>". $this->session->userdata('username')." 
	  	    			</label></br>
	  	    			<input type='text' id='comment' name='comment'/> <button id='saveComment' type='button'> comment </button></br></br>
	  	    		  </div><!--comment-box-->
	  	    		</div><!--user-->";
        }

        return $html;

    }


}



?>