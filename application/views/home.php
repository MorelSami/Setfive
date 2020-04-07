
<?php 
  $session_username = $this->session->userdata('username');
  $session_userId =  $this->session->userdata('userId');
?>
<div id="container" class="container-fluid">
<div id="header" class="row">
	<div class="col-11">
	  <h3>  <?php echo $login_msg; ?> </h3> 
	</div>
	<div class="col">
		<a href="logout"><span id="logout"><i class="fas fa-sign-out-alt"></i> Logout</span></a>
	</div>
</div>

  <div id="body" class="row">
	  <div id="jokes-box" class="col-8">
	  	 <div id="greetings" class="alert alert-success row">
		   <?php echo 'Hi '.$session_username.'!'; ?>
         </div>
		<div id="search_form" class="row">
            <?php  echo form_open('site/searchJokes', array('id'=>'searchJoke', 'class'=>'col')); ?>
               <fieldset>
                  <legend class="legend-css">Search Form</legend>
                  <br>
                  <label>Search Joke: <input type="text" id="key" name="key" value=""  required /><div id="error"></div></label><br><br>
                  <input type="radio" id="radio1" name="searchType" value="Keyword" checked/> Keyword
                  <input type="radio" id="radio2" name="searchType" value="Id"/> Id
                  <input type="radio" id="radio3" name="searchType" value="Rating"/> Rating
                  <input type="radio" id="radio4" name="searchType" value="AvgRate"/> Average Rating<br><br>
                  <input type="hidden" id="params" name="server" value="" />
                  <button type="button" id="search"> Search</button>
                 </fieldset>
             </form>
         </div><!--search_form-->
         <hr>
         <div id="jokes_list" class="row">
               <legend class="legend-css">List of Jokes </legend>
               <table id="tJokes" class="table table-hover table-style table-responsive" style="">
                 <thead >
                   <tr style="">
                    <th>JokeId</th><th scope="col-4" colspan="1">Jokes</th><th scope="col-4">Ratings</th><th colspan='2'>Average Ratings</th><th>Action(s)</th>
                   </tr>
                 </thead>
                 <tbody id="results">
                    <?php 
                    if(isset($jokesInfo)){
                      foreach ($jokesInfo as $info){
                       echo"<tr style='padding:0.5%'>
                              <td>".$info['jokeId']."</td>
                              <td colspan='8'>".$info['joke']."</td>
                              <td>".$info['rate']."</td>
                              <td colspan='2'>".$info['AvgRating']."</td>
                              <td><a id='".$info['jokeId']."' title='view joke id:".$info['jokeId']."' onclick='viewJoke(this.id)' ><img src='".base_url()."application/views/img/view.jpeg' alt='view joke' width='25px' height='25px'></a></td>
                            </tr>";
                        }
                       }
                       else {
                           echo "<tr><td colspan='6' style='color:red; text-align:center;'> No Jokes Found! </td></tr>";
                       }
                    ?>
                 </tbody>
               </table>
         </div><!--jokes_list-->

	</div><!--jokes-box-->

	<div id="user-box" class="col">
	  
	  <div id="jokebox" class="row">

	  	<?php  echo form_open("site/viewJoke", array("id"=>"updateJoke", "class"=>"col")); ?>

	      <div id="joke-info" class="row">
	      	<input type="hidden" id="rateId" name="rateId" value=""/>
	      	<input type="hidden" id="jokeId" name="jokeId" value=""/>
	      	<input type="hidden" id="userId" name="userId" value="<?php echo $this->session->userdata('userId');?>"/>
	      	<h5> Joke # <span id="joke-Id"> </span></h5>
	      	<div id="joke" class="alert alert-info row"></div><!--joke-->
	      	<div id="rate-div" class="form-group row">
	      	   <label> Rate:  </label> 
               <div class="col-sm-10">
               	  <input type="text" id="rate" class="" name="rate" value="" readonly="" /> 
               </div>
               <div class="col-sm-10">
                <span id="startRate"> 1 </span>
	      		  <input type="range" id="rate-range" class="" name="rate-range" min="1" max="5" value="3"/>
	      		<span id="endRate">5</span>
               </div>

	      	</div>
	      </div><!--joke-info-->

	      <div id="user-comments" class="row">
	  	    <h5> Comments   #<span id="totalComments"> 0 </span></h5>
	  	    <div id="comments" class="col">
	  	    	<div id="user-<?php echo $session_userId; ?>" class="row user">
	  	    		<div id="comment-0" class="col comment-box">
	  	    			<label><img src="<?php echo base_url();?>application/views/img/user_profile.png" alt="profile icon" width="50px"      height="50px"> <?php echo $session_username; ?> 
	  	    			</label></br>
	  	    			<input type="text" id="comment" name="comment" value=""/> <button id="saveComment" type="button"> comment </button></br></br>
	  	    			
	  	    		</div><!--comment-box-->
	  	    	</div><!--user-->
	  	    </div><!--comments-->
	      </div><!--user-comments-->

	      <div class="col">
	      	<button id="saveUpdatedJoke" type="button"> Save </button>	
	      </div>

	    </form>	

      </div><!--jokebox-->

	</div><!--user-box-->



 </div><!-- body -->

</div><!--container-->

<script>

$(document).ready(function(){
	
//performs the following action after form submission
$("#search").on('click',function(e) {
       
       var term= $('#key').val();
	   var type = $("input[name='searchType']:checked").val(); 
	   var msg = '';
	   var base_url = '<?php echo base_url(); ?>index.php/';

	   if(term == ''){
          msg = 'Please enter a search key!';
          $('#error').html(msg);
          $('#error').css('display', 'block');
	   }
       else{
          msg= term + '--' + type;
          console.log(msg);


       $.ajax({
       	  url: base_url + 'site/searchJokes',
       	  type:'POST',
       	  dataType: 'json',
       	  data: {
       	  	 option: type,
       	  	 value: term
       	  },
       	  success:function(res, status){
             
              console.log('Status :' + status);
              $('#results').html(res);   
       	  },
       	  error:function(res, status, error){

       	  	 console.log('error: ')
       	  	 console.log(status);
       	  }
         });
      }
	    
		
	});


//update the rate value field when rate range updated/ loaded
var rangeValue = $('#rate-range').val();
$('#rate').val(rangeValue);

$('#rate-range').on('change', function(){ 
    $('#rate').val(this.value);

});

//save the update joke info for the current user
 $('#saveUpdatedJoke').on('click', function(){

 	   
      var jokeId= $('#jokeId').val();
      
      if(jokeId == '')
      	return console.log('Joke not selected');

 	   console.log('Joke Update Initiated ... ');
       var rateId= $('#rateId').val();
       var jokeId= $('#jokeId').val();
	   var userId = $('#userId').val(); 
	   var rate = $('#rate').val();
	   var base_url = '<?php echo base_url(); ?>index.php/';


       $.ajax({
       	  url: base_url + 'site/updateJokeInfo',
       	  type:'POST',
       	  dataType: 'json',
       	  data: {
       	  	 rateId: rateId,
       	  	 jokeId: jokeId,
       	  	 userId: userId,
       	  	 rate: rate
       	  },
       	  success:function(res, status){
             
              console.log('Status :' + status);   
           
              if(res.status == 1)
              	$('#results').html(res.records); //update jokes table

             $('#greetings').html(res.msg);   //alert user with status
                
       	  },
       	  error:function(res, status, error){

       	  	 console.log('error: ')
       	  	 console.log(status);
       	  }
         });

  });

 //user comment specific joke function 
  $('#comments').on('click', '#saveComment', function(){


      var jokeId= $('#jokeId').val();
      var comment = $('#comment').val();
      var userId = $('#userId').val(); 
	  var base_url = '<?php echo base_url(); ?>index.php/';
	  //var userId = '<?php echo $session_userId; ?>';
      
      if(jokeId == '')
      	return console.log('Joke not selected!');
      else if(comment == '')
      	return console.log('Please enter your comment!');

       console.log('Joke Comment Initiated ... ');

       $.ajax({
       	  url: base_url + 'site/commentJoke',
       	  type:'POST',
       	  dataType: 'json',
       	  data: {

       	  	 jokeId: jokeId,
       	  	 userId: userId,
       	  	 comment: comment
       	  },
       	  success:function(res, status){
             
              console.log('Status :' + status);
              //console.log(res);

              if(res.status == 1){   
               $('#totalComments').html(res.numOfComments);
               $('#comments').html(res.records);
              }
                
       	  },
       	  error:function(res, status, error){

       	  	 console.log('error: ')
       	  	 console.log(status);
       	  }
        });

  });


});

//view a selected joke
function viewJoke(id){

   console.log('About to view joke #' + id);
   var base_url = '<?php echo base_url(); ?>index.php/'

   $.ajax({
       	  url: base_url + 'site/viewJoke',
       	  type:'POST',
       	  dataType: 'json',
       	  data: {
       	  	 option: 'Id',
       	  	 value: id
       	  },
       	  success:function(res, status){
             
            $('#user-box').css('display', 'block');
             
             console.log('Status :' + status);
             
             if(res.status == 1){
                
                //display selected joke info
                for( const [key, value] of Object.entries(res.jokeInfo[0])){
                 
                  if(key == 'userId')
                 	continue;
              	  else if(key == 'joke')
              	    $('#'+ key).html(value);
              	  else
              	    $('#'+ key).val(value);
               }

              $('span#joke-Id').html($('#jokeId').val());  

               //display all available comments for selected joke

               $('#totalComments').html(res.commentsInfo.count);
               $('#comments').html(res.commentsInfo.records);

               //notify user about the event
               $('#greetings').html(res.msg);
              
             }
               
       	  },
       	  error:function(res, status, error){

       	  	 console.log('error: ')
       	  	 console.log(status);
       	  }
       });
   
 }
</script>