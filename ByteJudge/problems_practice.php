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
	header('Location: ./login.php');
}
else
{
	$status=false;
	$errorstr="";
	
	include("includes/header.php");
	include("includes/nav.php");
	include('./includes/mysql_login_view.php');
	echo "
	<div id='content'>
	<h3 style='text-align:center';>
	Practice Problems
	</h3>
	<table id='table'>
	<tr>
		<th><strong>Problem Code</strong></th>
		<th><strong>Title</strong></th>
		<th><strong>Accepted Submissions</strong></th>
		<th><strong>Total submissions</strong></th>
		<th><strong>Solved By</strong></th>
	</tr>
	";
	
	$query="select * from problems where type='practice'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$status=false;
		$errorstr="Cannot access problems";
		goto end;
	}	
	$i=0;
	if($res)
	while($r=mysqli_fetch_array($res))
	{
		$problem_title=$r['problem_title'];
		$problem_code=$r['problem_code'];
		$solvedby=$r['solvedby'];
		$acceptedsubmissions=$r['acceptedsubmissions'];
		$totalsubmissions=$r['totalsubmissions'];
		
		echo "
		<tr";
		if($i%2==1)
			echo " class='alt'";
		echo ">
		
	 	<td>
			<a href='./viewproblem.php?pcode=$problem_code'>$problem_code</a>
	 	</td>
		<td>
	  		<label>$problem_title</label>
	 	</td>
		<td>$acceptedsubmissions</td>
		<td>$totalsubmissions</td>
		<td>$solvedby</td>
		</tr>
		";
		$i++;
	}
	
	end:
	
	if(!($status))
	{
		echo $errorstr;
	}
	echo "
	</table>
	</div> <!-- end #content -->
	";
	include("includes/sidebar.php"); 



	include("includes/footer.php");
}
?>

</div> <!-- End #wrapper -->

</body>

</html>
