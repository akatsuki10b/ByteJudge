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
if(!(isloggedin("admin")==true))
{
	header('Location: ./login.php');
}
else
{
	include("./includes/header.php");
	include("includes/nav.php");
	echo "
	<div id='content' style='margin-top: 20px'>
		<h3>Register faculty via csv</h3>
		<form method='post' enctype='multipart/form-data' action='./scripts/make_facultyfromlist.php'>
		Upload the CSV file containing the data about faculties.<br>
		The rows should adhere strictly to the following format:<br>
		Faculty ID,Full name,Date of birth,Email address,College,Branch,Designation[,group1,group2,...]<br>
		-> All fields should be in different columns in CSV file.<br>
		-> Faculty ID,Full name and Email are compulsary fields.<br>
		-> [,group1,group2] is optional list of groups that the faculty belongs to. These groups should be created in the database prior to uploading the file.<br>
		-> Initially the password of the newly created account is the faculty ID in upper case.<br>
		<br>
			<input type='file' name='faculty_file' />
			<input type='submit' value='Add Faculty' class='bbutton' style='margin-top: 20px' />
		</form>
	";
	if(isset($_GET['result']))
		echo $_GET['result'];
	echo 
	"
	</div> <!-- end #content -->
	";
	include("includes/sidebar.php"); 



	include("includes/footer.php"); 
}
?>
</div> <!-- End #wrapper -->

</body>

</html>
