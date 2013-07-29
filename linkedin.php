<?php 

//Api key and secret key
$api_key = 'yggexw2zwymv';
$secret_key = 'koNz91oIVqAsVJc2' ;

//Require Linkedin helper is created before
require 'helper/LinkedIn.php';

//Call back link when login at linkedin done
$call_back_link = "http://demo.dethoima.com/linkedin.php?login=linkedin";

//Init helper
$linkedin_helper = new LinkedIn($api_key, $secret_key, $call_back_link);

$info = null;
$user_info =null;
$profile_url = null;


if ( isset( $_GET['login'] ) && ( $_GET['login']  == 'linkedin') )
{
	//Call authentication method 
	$res = $linkedin_helper->authentication();

	//Check status
	if ($res['status'] == 'ok')
	{
		$res_info = $res['info'];
			
		//Expires login time
		if ($res_info->expires_in <= 0)
			$info = "Login fail";
		else
		{
			$user_info = $linkedin_helper->getUserInfo($res_info->access_token);
			$profile_url = $linkedin_helper->getProfileUrl($user_info['id']);
		}
	}
	else
	{
		$info ="Login fail";
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>LinkedIn Login Tutorial</title>
<link rel="author" href="https://plus.google.com/102005487368734658694" />
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/css/bootstrap-combined.min.css"	rel="stylesheet">
</head>
<style>
	.container{
		margin:20px auto;
	}
</style>
<body>
	<div class="container">
		<h2>Login your website by using LinkedIn API</h2>
		<div class="well">
			
			<?php
			if ( $info != null )
			{
				echo sprintf("<p class='alert alert-block'>%s</p>", $info);
			} 
			if ( $user_info != null )
			{
				//Login successful, var_dump result
				echo "<pre>";
				var_dump($user_info);
				echo "</pre>";
				var_dump($profile_url);
			}
			else
			{
				//You have not login yet
				echo sprintf("<a class='btn btn-primary' href='%s'>Login with linkedIn</a>", $linkedin_helper->loginUrl);
			}

			?>
		</div>
		<a href="http://dethoima.com">Come back home page</a>
	</div>
</body>
</html>
