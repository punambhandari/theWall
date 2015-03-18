<?php 
session_start();

 ?>

<html>
<head>
	<title>Login and Registration</title>
	<style>
#wrapper{
	width: 970px;
	vertical-align: top;
}

	#Regis,#Login{
	display: inline-block;
	width: 400px;
	border-style: solid;
	border-color:#3E5996 ;
	border-width: 1px;
	border-radius: 10px;
	margin: 10px;
	padding: 10px;
	vertical-align: top;
	font-family: 'Marker Felt', 'Comic Sans', sans-serif;
	font-size: 12p;
	}
	#Regis{
		background-color: #8EA9E6;
		color: #151B54;
	}
	#Login{
		background-color: #B6B6B4;
		color: #151B54;
	}
	form{ 
		display: table;  
		padding: 10px;}
	p{ 
		display: table-row; padding: 
	}
	label{ 
		display: table-cell; 
		width: 200px;
		margin: 5px;}
	input{ display: table-cell; 
		margin: 5px; 
		width: 200px;}
	input[type="submit"]{
	width: 80px;
	height: 20px;
	border-radius: 5px;
	border-style: solid;
	border-width: 1px;
	border-color: black;
	float: right;
	margin-top: 30px;
	}
		#errors,#message{
		display:inline-block;
		vertical-align: top;
		width: 300px;
		padding: 10px;
		border-width: 1px;
		border-style: solid;
		margin: 10px;
		border-radius: 10px;
		}
		.error{
		font-size: 10px;
		color: red;
		font-family: arial;
		}
		
		.success{
		font-size: 12px;
		color: green;
		font-family: arial;
		}
		.highlight{
		border-color: #E55451;
		box-shadow: 0 0 10px #9ecaed;
  	
		}
	
	</style>
</head>
<body>

<?
function highlight($name)
{
if(isset($_SESSION['errors']))
	{
		//var_dump($_SESSION['errors']);
		if(array_key_exists($name , $_SESSION['errors']))
		{
			echo "class='highlight'";
			
		}
	}
}

if(isset($_SESSION['errors']))
{	
		
	echo "<div id='errors'>";
	//var_dump($_SESSION['errors']);
	foreach ($_SESSION['errors'] as $error) {
		echo "<p class='error'> - ".$error."</p>";
	}
	echo "</div>";
	
}
if(isset($_SESSION['message']))
{	
	echo "<div id='message'>";
	
	echo "<p class='success'> - ".$_SESSION['message']."</p>";
	
	echo "</div>";
	unset($_SESSION['message']);	
}




?>
</div>
<div id="wrapper">
	<div id="Regis">
		<h2>Register!</h2>
		<form method="post" action="process.php">
			<p>
				<label for "email">Email * :</label>
				<input <?php highlight("email") ?> type="text" name="email" value=<?php if(isset($_SESSION['email'])){echo "'".$_SESSION['email']."'"; } ?>>
				
			</p>
			<p>
				<label for "first_name">First Name * :</label>
				<input <?php highlight("first_name")  ?> type="text" name="first_name" value=<?php if(isset($_SESSION['first_name'])){echo "'".$_SESSION['first_name']."'"; } ?>>
			</p>
			<p>
				<label for "last_name">Last Name * :</label>
				<input <?php highlight("last_name")  ?> type="text" name="last_name" value=<?php if(isset($_SESSION['last_name'])){echo "'".$_SESSION['last_name']."'"; } ?>>
			</p>
			<p>
				<label for "password">Password * :</label>
				<input <?php highlight("password")  ?> type="password" name="password" >
			</p>
			<p>
				<label for "confirm_password">Confirm Password * :</label>
				<input <?php highlight("confirm_password")  ?> type="password" name="confirm_password">
			</p>
			
			<p>
				<input type="hidden" name="action" value="register">
				<input type="submit" value="Register">
			</p>
		</form>
	</div>
	<div id="Login">
		<h2>Login!</h2>
		<form method="post" action="process.php">
			<p>
				<label for "email">Email * :</label>
				<input type="text" name="email" >
			</p>
			<p>
				<label for "password">Password * :</label>
				<input type="password" name="password" >
			</p>
			
			<p>	
				<input type="hidden" name="action" value="login">
				<input type="submit" value="Login">
			</p>
	    </form>
	</div>
</div>
<?php 
unset($_SESSION['errors']);?>
</body>
</html>