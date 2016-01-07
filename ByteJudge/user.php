<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>

<head>

<meta http-equiv='content-type' content='text/html; charset=utf-8' />

<meta name='description' content='' />

<meta name='keywords' content='' />

<meta name='author' content='' />

<link rel='stylesheet' type='text/css' href='css/style.css' media='screen' />

<title>ByteJudge</title>

</head>

<body>
<div id='wrapper'>
<?php 
require('./scripts/determineidentityfunc.php');
if(!(isloggedin("any")==true))
{
	header('Location: ./login.php');
}
else
{
	include('includes/header.php');
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	include('includes/nav.php');
	$status=true;
	$errorstr='';
	$user=$_GET['user'];
	$user=strtoupper($user);
	include('./includes/mysql_login_view.php');
	$user=strip_tags(mysqli_real_escape_string($db,$user));
	$query="select type from users where userid='$user'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$status=false;
		$errorstr="Error in accessing Database";
		$errorstr=mysqli_error($db);
		goto end;
	}
	if(mysqli_num_rows($res)!=1)
	{	
		$status=false;
		$errorstr="No user exists by username $user";
		goto end;	
	}
	$res=mysqli_fetch_array($res);
	$type=$res['type'];
	$page='';
	if($type=="student")
		$page="student_profile.php?rollno=";
	else if($type=="faculty")
		$page="faculty_profile.php?facultyid=";
	else if($type=="admin")
		$page="admin_profile.php?adminid=";
	if($page=="")
	{	
		$status=false;
		$errorstr="Error occurred";
		goto end;
	}
	echo "Location: ./$page$user";
	header("Location: ./$page$user");
	end:
	if(!($status))
		echo $errorstr;
	
	include('includes/footer.php'); 
}
?>
</div> <!-- End #wrapper -->

</body>

</html>
