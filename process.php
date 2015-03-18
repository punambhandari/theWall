<?php
session_start();
require_once('new-connection.php');
//$_SESSION=array();
if(isset($_POST['action']) && $_POST['action']=="register")
{
	validate_register($_POST);
}
else if(isset($_POST['action']) && $_POST['action']=="login")
{
	validate_login($_POST);
}
else if(isset($_POST['action']) && $_POST['action']=="post_message")
{
	validate_post_message($_POST);
}
else if(isset($_POST['action']) && $_POST['action']=="post_comment")
{
	validate_post_comment($_POST);
}
else if(isset($_POST['action']) && $_POST['action']=="delete")
{
	Delete($_POST);
}

else//implemented for logoff
{
	session_destroy();
	header('Location:index.php');
	die();

}


//Validation functions


function validate_register($post)
{
	if(empty($post['email']))
	{
		$_SESSION['errors']['email']="email is required!";
	}
	else
	{
		$_SESSION['email']=$_POST['email'];
	}

	if(empty($post['first_name']))
	{
		$_SESSION['errors']['first_name']="first name is required!";
	}
	else
	{
		$_SESSION['first_name']=$_POST['first_name'];
	}
	if(empty($post['last_name']))
	{
		$_SESSION['errors']['last_name']="last name is required!";
	}
	else
	{
		$_SESSION['last_name']=$_POST['last_name'];
	}
	if(empty($post['password']))
	{
		$_SESSION['errors']['password']="password is required!";
	}
	if(empty($post['confirm_password']))
	{
		$_SESSION['errors']['confirm_password']="last name is required!";
	}

	if($post['password']!=$post['confirm_password'])
	{
		$_SESSION['errors']['password']="password and confirm password does not match!";
	}


	if(strlen($post['password'])<=6)
	{
		$_SESSION['errors']['password']="password has to be more than 6 characters!";
	}

	if(!filter_var($post['email'],FILTER_VALIDATE_EMAIL))
	{
		$_SESSION['errors']['email']="email format is not correct!";
	}

	//check if user already exists
	$query="select email from users where email='{$_POST['email']}'";
		
	//var_dump($query);
	$result =fetch_record($query);
	//var_dump($result);
	if(count($result)>0)
	{
			//user exists
	$_SESSION['errors']['email']="username (".$_POST['email'].") already exists!";
		
	}

	if(isset($_SESSION['errors']) && count($_SESSION['errors'])>0)
	{
		header('Location:index.php');
		die();
	
	}
	else
	{

	$email=escape_this_string($_POST['email']);
	$first_name=escape_this_string($_POST['first_name']);
	$last_name=escape_this_string($_POST['last_name']);
	$password=escape_this_string($_POST['password']);
	$salt = bin2hex(openssl_random_pseudo_bytes(22));
	$encrypted_password = crypt($password, $salt);
	
	//generate a salt.

		//  insert into db
	$query="INSERT INTO users(email,first_name,last_name,password,created_at,updated_at) 
	VALUES('$email','$first_name',
	'$last_name','$encrypted_password',NOW(),NOW())";
	
	//var_dump($query);
	run_mysql_query($query);
	$_SESSION['message'] ="Congratulations! user is successfully registered!";
	header('Location:index.php');
	die();
	}
	
}

function validate_login($post)
{
	if(empty($post['email']))
	{
		$_SESSION['errors'][]="email is required!";
	}
	if(empty($post['password']))
	{
		$_SESSION['errors'][]="password is required!";
	}
	if(isset($_SESSION['errors']) && count($_SESSION['errors'])>0)
	{
		header('Location:index.php');
		die();
	
	}
	else

	{
		//check if user already exists
		$email=escape_this_string($_POST['email']);
		$password=escape_this_string($_POST['password']);
		$query="select id,email,password,first_name  from users where email='$email'";
			
		//var_dump($query);
		$result =fetch_record($query);
		//var_dump($result);
		
		if(count($result)>0)
		{
		//encrypt the password and compare with what exists in db
		$encrypted_password=crypt($password, $result['password']);
		
			if($encrypted_password==$result['password'])
			{
			//password matches
			$_SESSION['username'] =$result['first_name'];
			$_SESSION['user_id'] =$result['id'];
			$_SESSION['email'] =$result['email'];

			header('Location:main.php');
			die();

			}
			else
			{

			$_SESSION['errors'][]="username and password do not match!";
			header('Location:index.php');
			die();
			
			}
		}
		else
		{

		$_SESSION['errors'][]="username does not exist!";
		header('Location:index.php');
		die();

		}

	}
		

}

function validate_post_message($post)
{

	if(empty($_POST['post_message']))
	{
		$_SESSION['errors'][]="message is empty!";

	}

	if(isset($_SESSION['errors']) && count($_SESSION['errors'])>0)
	{

		header('Location:main.php');
		die();
	}
	else
	{
		//insert into the database
		//code to prevent from sql injection
		$post_message=escape_this_string($_POST['post_message']);


		$query="INSERT INTO  messages (user_id,message,created_at,updated_at) 
				values('{$_SESSION['user_id']}','$post_message',NOW(),NOW())";

		if(run_mysql_query($query))
		{
		$_SESSION['message']="great! you  have posted a new message in the Wall!";
		header('Location:main.php');
		die();
		// navigate to the main page
		}
		else
		{
			$_SESSION['message']="failed to insert the post!";
		}
	}
}

function validate_post_comment($post)
{


	if(empty($post['post_comment']))
	{
		$_SESSION['errors'][]='comment is empty!';
	}

	if(isset($_SESSION['errors']) && count($_SESSION['errors'])>0)
	{
		header('Location:main.php');
		die();
	}
	else
	{
		$post_comment=escape_this_string($_POST['post_comment']);
		$query="INSERT INTO  comments (message_id,user_id,comment,created_at,updated_at) 
				values('{$_POST['message_id']}','{$_SESSION['user_id']}','$post_comment',NOW(),NOW())";

	if(run_mysql_query($query))
		{
		$_SESSION['message']="great! you  have posted a new comment in the Wall!";
		header('Location:main.php');
		die();
		// navigate to the main page
		}
		else
		{
			$_SESSION['message']="failed to insert the comment!";
		}

	}
}


function Delete($post)
{
	$query_delete_comment = "DELETE from comments where message_id='{$_POST['message_id']}'";
	$query_delete_message = "DELETE from messages where id='{$_POST['message_id']}'";
	$query_select_message="SELECT * from messages where id='{$_POST['message_id']}'";


	run_mysql_query($query_delete_comment);
	
	run_mysql_query($query_delete_message);
	if(!fetch_record($query_select_message))
	{
		$_SESSION['message']='message successfully deleted!';
	}
	else
	{
		$_SESSION['message']='error deleting the message. please contact support';
	}

		header('Location:main.php');
		die();

}

?>