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
	
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	include("./includes/header.php");
	include("./includes/nav.php");
	
	include('./includes/mysql_login_view.php');
	$username='';
	$usertype='';
	if(isset($_SESSION['username']))
		$username=$_SESSION['username'];
	if(isset($_SESSION['type']))
		$usertype=$_SESSION['type'];

	$errorstr='';
	$status=true;
	
        $probcode='';	

        $testid='';
	$istest=false;

	$show=false;
	echo '	
		<div id="content">
	';
        if(isset($_GET['pcode']))
        {
			$probcode=strip_tags(mysqli_real_escape_string($db,$_GET['pcode']));
			$probcode=strtoupper($probcode);
			$res=mysqli_query($db,"select * from problems where problem_code='$probcode'");
			if(!($res))
			{
				$status=false;
				$errorstr="Error in query";
				goto end;
			}
			if(mysqli_num_rows($res)!=1)
			{
				$status=false;
				$errorstr="Problem $probcode does't exist";
				goto end;
			}
		
			$r=mysqli_fetch_array($res);
			if($r['type']=="practice" || ($_SESSION['type']=="faculty" || $_SESSION['type']=="admin"))
			{
				$show=true;
			}
			else
			{
				$errorstr="Problem not available for viewing";
			}
      	}
	else if(isset($_POST['attempt']) )
	{	
		$istest=true;
		if(isset($_POST['problem_code']))
			$probcode=strip_tags(mysqli_real_escape_string($db,$_POST['problem_code']));
		if(isset($_POST['testid']))
			$testid=strip_tags(mysqli_real_escape_string($db,$_POST['testid']));
		if($_SESSION['type']=="student")
		{
			include('./scripts/isstudentauthorised_test.php');
			$res=isstudentauthorised($db,$username,$testid);
			if($res==2 || $res==3)
			{
				//echo "not allowed";
				header("Location: ./test.php?testid=$testid");
				goto end;
			}
			else if($res==1)
			{
				if(isproblemintest($db,$probcode,$testid)==1)
					$show=true;
				else
					$show=false;
			}
			else
			{
				echo $res;
				goto end;
			}
		}
		else if($_SESSION['type']=="student" || $_SESSION['type']=="faculty")
		{
			$errorstr="";
			$show=true;
		}
	}
	
	echo "<br>";
	if($show!=true)
		goto end;
	if($istest==true)
		echo "<a href='./test.php?testid=$testid'><input type='button' value='Back to Problems' class='bbutton' ></a>";
	echo "<br><a href='./user_submissions.php?pcode=$probcode' class='bbutton'>View Submissions </a>";
	
	$languagesallowed=array();
	$query="select language from problems_languagesallowed where problem_code='$probcode'";
	$res=mysqli_query($db,$query);
	while($l=mysqli_fetch_array($res))
	{
		$s=$l['language'];
		$languagesallowed[]=$s;
		//echo $s;
	}
	if(file_exists("./PROBLEMS/$probcode/title"))
     		$problemtitle=nl2br(file_get_contents("./PROBLEMS/$probcode/title"));
	else
		$problemtitle='';
	if(file_exists("./PROBLEMS/$probcode/statement"))
		$problemstatement=nl2br(file_get_contents("./PROBLEMS/$probcode/statement"));
	else
		$problemstatement='';
	if(file_exists("./PROBLEMS/$probcode/input_desc"))
		$input_desc=nl2br(file_get_contents("./PROBLEMS/$probcode/input_desc"));
	else
		$input_desc='';
	if(file_exists("./PROBLEMS/$probcode/output_desc"))
	      	$output_desc=nl2br(file_get_contents("./PROBLEMS/$probcode/output_desc"));
	else
		$output_desc='';
	if(file_exists("./PROBLEMS/$probcode/constraints"))
		$constraints=nl2br(file_get_contents("./PROBLEMS/$probcode/constraints"));
	else
		$constraints='';
	if(file_exists("./PROBLEMS/$probcode/sampleip"))
	      	$sampleip=nl2br(file_get_contents("./PROBLEMS/$probcode/sampleip"));
	else
		$sampleip='';
	if(file_exists("./PROBLEMS/$probcode/sampleop"))
	     	$sampleop=nl2br(file_get_contents("./PROBLEMS/$probcode/sampleop"));
	else
		$sampleop='';
	if(file_exists("./PROBLEMS/$probcode/sampleexp"))
	     	$sampleexp=nl2br(file_get_contents("./PROBLEMS/$probcode/sampleexp"));
	else
		$sampleexp='';
	
      	include("./includes/page_title.php");
      	include("./includes/page_content.php"); 
     	include("./includes/sample_io.php"); 
	include("./includes/sample_exp.php");
		
	include("./includes/submit.php"); 
	

	end:
		echo "$errorstr
	</div> <!-- end #content -->
	";
      include("./includes/sidebar.php");
    
	      
      include("./includes/footer.php"); 
}
?>
</div> <!-- End #wrapper -->

</body>

</html>
