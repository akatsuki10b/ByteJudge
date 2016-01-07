<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<meta name="description" content="" />

<meta name="keywords" content="" />

<meta name="author" content="" />

<link rel="stylesheet" type="text/css" href="css/style.css" media="screen" />

<title>ByteJudge</title>

</head>

<body>
<div id="wrapper">
<?php
require('./scripts/determineidentityfunc.php');
if(!(isloggedin("any")==true))
{
	header('Location: ./home.php');
}
else
{
	include("./includes/header.php");
	include("includes/nav.php");
	include('./includes/mysql_login_test.php');
	echo '
	<div id="content">
	';
	$errorstr="";
	$testid='';
	if(isset($_GET['testid']))
		$testid=strip_tags(mysqli_real_escape_string($db,$_GET['testid']));
	$testid=strtoupper($testid);
	$username=$_SESSION['username'];
	$show=false;
	
	if($_SESSION['type']=="admin" || $_SESSION['type']=="faculty")
		$show=true;
	else
	{
		$query="select * from test_attemptedby where rollno='$username' and testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Error in prequery";
			
		}
		else if(mysqli_num_rows($res)!=1)
		{
			$errorstr="You are not authorised to access the leaderboard";
			
		}
		else
			$show=true;
	}
	if($show==true)
		include("./includes/leaderboard_table.php");
	else
		echo $errorstr;
	
	echo '
	</div> <!-- end #content -->
	';
	include("./includes/sidebar.php");
	include("./includes/footer.php"); 
}
?>

</div> <!-- End #wrapper -->

</body>

</html>
