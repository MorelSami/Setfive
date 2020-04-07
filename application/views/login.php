

<div id="container" class="container-fluid">
	<h3>Please Login below !!</h3>

	<div id="body" class="row">

		<div id="loginMsg" class="alert alert-danger">
			<p><?php echo isset($login_msg) ? $login_msg : ''; ?></p>
		</div>

       <?php  echo form_open('site/index', array('id'=>'loginForm', 'class'=>'col-6')); ?>

          <label> Username(email):</label> <input type="email" id="username" name="username"  required/></br>
          <label> Password:       </label> <input type="password" id="upass" name="upass"  required/></br>
          <label> <input type="checkbox" id="savelogin" name="remember" /> Remember Me</label></br></br>
          <button type="submit"> Sign In</button>
        </form>

    </div>

</div><!--container-->

<script type="text/javascript">
  
  $(document).ready(function(){

     var valid = '<?php echo isset($flag) ? $flag : 1;?>';
     
      if(valid == 0)
        $('#loginMsg').css('display', 'block');
      else
        $('#loginMsg').css('display', 'none'); 

  });

</script>

