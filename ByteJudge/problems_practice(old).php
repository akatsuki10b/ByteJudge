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
	ini_set('display_errors',1);
ini_set('display_startup_errors',1);
	include("includes/header.php");
	include("includes/nav.php"); 
	include('./includes/mysql_login_user.php');
	$query="select problem_code,problem_title from problems where visibility='true' and type='practice'";
	$res=mysqli_query($db,$query);
	
	
?>

<div id='content'>
<h3 style='text-align:center';>
All Problems
</h3>
<table id='table'>
<tr>
	<th><strong>Problem</strong></th>
	<th><strong>Code</strong></th>
	<th><strong>Points</strong></th>
	<th><strong>Accuracy</strong></th>
	<th><strong>Solved By</strong></th>
</tr>
<?php
	$i=1;
	while($row=mysqli_fetch_array($res))
	{
		$title=$row['problem_title'];
		$code=$row['problem_code'];
		if($i%2==0)
			echo "<tr>";
		else
			echo "<tr class='alt'>";
		echo "
		<td><a href='./viewproblem.php?pcode=$code' style='text-decoration:none'>$title</a></td>
		<td>$code</td>
		<td>350</td>
		<td>1.5</td>
		<td>4</td>
		</tr>
		 ";
		
	}
?>

</table>
</div> <!-- end #content -->
<?php include("includes/sidebar.php"); ?>



<?php include("includes/footer.php"); ?>
</div> <!-- End #wrapper -->

</body>

</html>
