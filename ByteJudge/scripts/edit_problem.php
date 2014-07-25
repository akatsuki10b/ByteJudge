<?php
require('./determineidentityfunc.php');
if(!(isloggedin("faculty")==true))//change so that admin can too add problem
{
	header('Location: ./../home.php');
}
else
{

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	include('./../includes/mysql_login_faculty.php');//change this later
	error_reporting(-1);
	mysqli_autocommit($db,false);
	$problem_code='';
	$problem_title='';
	
	$time_limit='';
	$memory_limit=1000000;
	$type='';
	
	$problem_showmistakes='false';
	$problem_visiblesolutions='false';
	$problem_type='';
	$languagesallowed=array();
	
	$status=true;
	$errorstr='';
	
	if(!isset($_POST['problem_code']))
	{
		$status=false;
		$errorstr="No problem code provided";
		goto end;
	}
	$problem_code=strip_tags(mysqli_real_escape_string($db,$_POST['problem_code']));
	if(isset($_POST['time_limit']))
		$time_limit=strip_tags(mysqli_real_escape_string($db,$_POST['time_limit']));
	if($time_limit=='')
		$time_limit=0;
	if(isset($_POST['memory_limit']))
		$memory_limit=strip_tags(mysqli_real_escape_string($db,$_POST['memory_limit']));
	if($memory_limit=='')
		$memory_limit=1000000;
	if(isset($_POST['problem_title']))
		$problem_title=strip_tags(mysqli_real_escape_string($db,$_POST['problem_title']));
	
	
	if(isset($_POST['problem_showmistakes']))
		$problem_showmistakes=strip_tags(mysqli_real_escape_string($db,$_POST['problem_showmistakes']));
	if(isset($_POST['problem_visiblesolutions']))
		$problem_visiblesolutions=strip_tags(mysqli_real_escape_string($db,$_POST['problem_visiblesolutions']));
	if(isset($_POST['problem_type']))
		$problem_type=strip_tags(mysqli_real_escape_string($db,$_POST['problem_type']));
	
	if(isset($_POST['languagesallowed']))
		$languagesallowed=$_POST['languagesallowed'];


	
	$problem_code=strtoupper($problem_code);
	
	

	$query="select problem_code from problems where problem_code='$problem_code'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$errorstr="error in prequery";
		$status=false;
	//	$errorstr.=mysqli_error($db);
		goto end;
	}
	$res=mysqli_affected_rows($db);
	if($res!=1)
	{
		$errorstr="Problem by code $problem_code doesn't exists";
		$status=false;
		goto end;
	}
	
	$query="update problems set timelimit=$time_limit,memorylimit=$memory_limit,problem_title='$problem_title',showmistakes='$problem_showmistakes',visiblesolutions='$problem_visiblesolutions',showmistakes='$problem_showmistakes',type='$problem_type' where problem_code='$problem_code'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$status=false;
		$errorstr="Error in updating details";
		goto end;
	}
	$query="delete from problems_languagesallowed where problem_code='$problem_code'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$status=false;
		$errorstr="Error in deleting old details";
		goto end;
	}
	$query="insert into problems_languagesallowed(problem_code,language) values('$problem_code',";
	foreach($languagesallowed as $langs)
	{
		$res=mysqli_query($db,$query."'$langs')");
		if(!($res))
		{
			$status=false;
			$errorstr="Error in updating languages allowed";
			goto end;
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		header("Location: ./../new_problem.php?prev=done");
	}
	else
	{
		echo mysqli_error($db);
		mysqli_rollback($db);
		mysqli_close($db);
		echo "$errorstr";
		//header("Location: ./../new_problem.php?prev=fail&msg=$errorstr");
	}
	;
}
?>
