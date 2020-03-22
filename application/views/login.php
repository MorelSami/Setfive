<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to Dad's Jokes</title>

	<style type="text/css">

	#body {
		margin: 0 15px 0 15px;
	}
    
    h3{
    	margin-left: 10px;
    }
	#container {
		width: 60%;
		margin: auto;
		border: 1px solid #D0D0D0;
		box-shadow: 0 0 8px #D0D0D0;
	}

	#loginForm{
		margin-top: 5px;
		padding: 2%;
	}

	</style>
</head>
<body>

<div id="container" class="container-fluid">
	<h3>Please Login below !!</h3>

	<div id="body" class="row">
       <?php  echo form_open('site/index', array('id'=>'loginForm', 'class'=>'col-6')); ?>

          <label> Username(email):</label> <input type="email" id="username" name="username" /></br>
          <label> Password:       </label> <input type="password" id="upass" name="upass" /></br>
          <label> <input type="checkbox" id="savelogin" name="remember" /> Remember Me</label></br></br>
          <button type="submit"> Sign In</button>
        </form>

    </div>

</div><!--container-->

</body>
</html>