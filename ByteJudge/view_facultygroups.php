<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<meta name="description" content="" />

<meta name="keywords" content="" />

<meta name="author" content="" />

<link rel="stylesheet" type="text/css" href="style.css" media="screen" />

<title>ByteJudge</title>

</head>

<body>
<div id="wrapper">
<?php
require('./scripts/determineidentityfunc.php');
if(!(isloggedin("admin")==true))
{
	header('Location: ./home.php');
}
else
{
	include("./includes/header.php");
	include("includes/nav.php");
	echo '
	<div id="content">
	';
	include("./includes/view_facultygroups_form.php");
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
