

<div id="container" class="container-fluid">

   <h3>  <?php echo $login_msg; ?> </h3>

  <div id="body" class="row">
	  <div id="jokes-box" class="col-8">
	  	 <div id="greetings" class="alert alert-success row">
		   <?php echo 'Hi '.$userInfo['username'].'!'; ?>
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
	      	<input type="hidden" id="jokeId" name="jokeId" value=""/>
	      	<input type="hidden" id="userId" name="userId" value=""/>
	      	<h4 id="joke-id"> Joke # <span></span></h4>
	      	<div id="joke" class="alert alert-info row"></div><!--joke-->
	      	<div id="rate" class="row"></div>
	      </div><!--joke-info-->

	      <div id="user-comments" class="row">
	  	    <h4> Comments   <span>#</span></h4>
	  	    <div id="comments" class="col">
	  	    	<div id="user" class="row">
	  	    		<div id="comment-box" class="col">
	  	    			<label><img src="<?php echo base_url();?>application/views/img/user_profile.png" alt="profile icon" width="50px"      height="50px"> Username 1 day ago
	  	    			</label></br>
	  	    			<input type="text" id="comment" name="comment" value=""/></br></br>
	  	    			<button id="submitComment" type="button"> Submit</button>
	  	    		</div><!--commnet-box-->
	  	    	</div><!--user-->
	  	    </div><!--comments-->
	      </div><!--user-comments-->

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
	   var base_url = '<?php echo base_url(); ?>index.php/'

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
              console.log(res);   
       	  },
       	  error:function(res, status, error){

       	  	 console.log('error: ')
       	  	 console.log(status);
       	  }
       });
   
 }
</script>