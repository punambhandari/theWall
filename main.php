<?php
session_start();
require_once('new-connection.php');
date_default_timezone_set('America/Los_Angeles');


?>

<html>
<head>
	<title>Welcome to the Wall!</title>
	<script src= 'http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js'></script>
	<script type="text/javascript" src="script.js"></script>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<div id="wrapper">
	<div id="wall">
		<h1 >CodingDojo Wall</h1>
	<?php 
		if(isset($_SESSION['username']))
		{

			echo "<h5> Welcome ".$_SESSION['username']."!</h5>";
		}
		echo "<a href='process.php'>Logoff</a>"
	?>
	</div>


	<form method="post" action="process.php">
		<div class="messagebox">
			<?php if(isset($_SESSION['errors']))
			{
				foreach ($_SESSION['errors'] as $error) {
					echo "<p class='errors'>".$error."</p>";
				}
				unset($_SESSION['errors']);
			}
			if(isset($_SESSION['message']))
			{
				echo "<p class='success'>".$_SESSION['message']."</p>";
				unset($_SESSION['message']);
			}
			?>
			<h4>Post a message</h4>
			<textarea name="post_message"></textarea>
			<input type="submit" value="Post a message">
			<input type="hidden" name="action" value="post_message">
		</div>
	</form>
	<div class="showmessages">
	<ul >
		<li><a  href="main.php?rows=<?php echo '10'; ?>">display last 10 posts (default)</a> </li>
		<li>|</li>
		<li><a href="main.php?rows=<?php echo '500'; ?>">display all posts</a></li>
	</ul>

	<?php
	//add button for 
	//get all the messages from the DB and show
	if(isset($_GET['rows']))
	{
	$rows=intval($_GET['rows']);

	}
	else
	{
	$rows=10;
	}

			if($rows==10)
			{
			$query_message="SELECT m.id as message_id, m.user_id as user_id, CONCAT_WS(' ',u.first_name,u.last_name) 
				as message_user, m.message as message ,m.created_at as messages_created_date from messages m  
				left join users u on m.user_id =u.id order by message_id desc LIMIT {$rows}";
			}
			else
			{
			$query_message="SELECT m.id as message_id, m.user_id as user_id, CONCAT_WS(' ',u.first_name,u.last_name) 
				as message_user, m.message as message ,m.created_at as messages_created_date from messages m  
				left join users u on m.user_id =u.id LIMIT {$rows}";
		
			}
			//var_dump($query_message);
			$results_message=fetch_all($query_message);
			if(count($results_message)>0)
			{
				if($rows==500)
				{
				$results_message = array_reverse($results_message);
				}
				foreach ($results_message as  $result) 
				{
					echo "<div class='display_post'><image class='postmessage' src='post.png'><h5 class='message'>".$result['message_user']." - 
					".date_format(date_create($result['messages_created_date']),'l jS \of F Y')."</h5>
					<p class='message'>".$result['message']."</p>";
					$message_id=$result['message_id'];
					$message_user_id=$result['user_id'];

					//calculate interval
					$datetime_messagecreated = date_create($result['messages_created_date']);
					//var_dump($datetime_messagecreated)
					//var_dump($datetime_messagecreated);
					//$datetime_now = date('m/d/Y h:i:s a', time());
					$datetime_now=new DateTime("now");
					//var_dump($datetime_now);	
					$interval = date_diff($datetime_now, $datetime_messagecreated);
					$interval= $interval->format('%d%h%i');
					//var_dump($interval);

					//create a new form with hidden fields and delete button if the post belongs to current user
					if(($_SESSION['user_id']==$message_user_id) && intval($interval)<=30)
					{

					echo "<form  method='post' action='process.php'>
								<input id='delete' name='delete' type='submit' value='delete'>
								<input type='hidden' name='action' value='delete'>
								<input type='hidden' name='message_id' value=" ."'".$message_id. "'".">
						</form>";
					}
					echo "</div>";
					$query_comment="SELECT  CONCAT_WS(' ',u.first_name,u.last_name) as comment_user,c.comment 
									as comment ,c.created_at as comment_created_date from comments c left join 
									users u on c.user_id =u.id where message_id= {$result['message_id']}";

					
					$results_comment=fetch_all($query_comment);
					if($results_comment)
						{
							foreach ($results_comment as  $result) 
							{
							echo "<img class='postcomment' src='comment.png'><h5 class='comment'>".$result['comment_user']." - 
							".date_format(date_create($result['comment_created_date']),'l jS \of F Y')."</h5>
							<p class='displaycomment'>".$result['comment']."</p>";

							}

						}

					echo "<form method='post' action='process.php'> <div class='commentbox'>
					<h4>Post a comment</h4><textarea name='post_comment'></textarea>
					<input type='submit' value='Post a comment'>
					<input type='hidden' name='message_id' value=" ."'".$message_id. "'".
					"><input type='hidden' name='action' value='post_comment'></div></form>";
					
				}

			}
		

	?>
			
		</div>

</div>
	
</body>
</html>