<?php
require('./determineidentityfunc.php');
if(!(isloggedin("admin")==true || (isloggedin("student")==true && isset($_POST['edit']) && isset($_POST['rollno']) && strtoupper($_POST['rollno'])==$_SESSION['username'])   ))
{
	
	header('Location: ./../home.php');
}
else
{
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	include('./../includes/mysql_login_admin.php');
	error_reporting(-1);
	mysqli_autocommit($db,false);
	$rollno='';
	$fullname='';
	$dob='';
	$emailid='';
	$college='';
	$branch='';
	$errorstr='';
	$password='';
	$confirmpassword='';
	$status=true;
	
	$edit=false;
	if(isset($_POST['edit']))
	{
		$edit=true;
	}
	if(isset($_POST['rollno']))
		$rollno=strip_tags(mysqli_real_escape_string($db,$_POST['rollno']));
	if(isset($_POST['fullname']))
		$fullname=strip_tags(mysqli_real_escape_string($db,$_POST['fullname']));
	if(isset($_POST['dob']))
		$dob=strip_tags(mysqli_real_escape_string($db,$_POST['dob']));
	if(isset($_POST['emailid']))
		$emailid=strip_tags(mysqli_real_escape_string($db,$_POST['emailid']));
	if(isset($_POST['college']))
		$college=strip_tags(mysqli_real_escape_string($db,$_POST['college']));
	if(isset($_POST['branch']))
		$branch=strip_tags(mysqli_real_escape_string($db,$_POST['branch']));
	if(isset($_POST['password']))
		$password=strip_tags(mysqli_real_escape_string($db,$_POST['password']));
	if(isset($_POST['confirmpassword']))
		$confirmpassword=strip_tags(mysqli_real_escape_string($db,$_POST['confirmpassword']));

	$rollno=strtoupper($rollno);
	//echo "$rollno $fullname";
	if($rollno=='' || $fullname=='' || ($password=='' && $edit==false))
	{
		$errorstr="not enough details to create new student";
		$status=false;
		goto end;
	}
	
	if($password!=$confirmpassword)
	{
		$errorstr="passwords do not match";
		$status=false;
		goto end;
	}
	$query="select userid from users where userid='$rollno'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$errorstr= "error in prequery";
		$status=false;
		goto end;
	}
	$res=mysqli_num_rows($res);
	if($edit)
	{
		
		if($res!=1)
		{
			$errorstr="Student with rollno $rollno doesn't exist";
			$status=false;
			goto end;
		}
		$query="select rollno from students_main where rollno='$rollno'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Error in prequery";
			$status=false;
			goto end;
		}
		$res=mysqli_num_rows($res);
		if($res!=1)
		{
			$errorstr="Student with rollno $rollno doesn't exist";
			$status=false;
			goto end;
		}

		$query="update students_main set fullname='$fullname',dob='$dob',emailid='$emailid',college='$college', branch='$branch' where rollno='$rollno'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Couldn't update details of student $rollno";
			$status=false;
			goto end;
		}
	}
	else
	{
		
		if($res>0)
		{	
			$errorstr= "user by userid $rollno already exists";
			$status=false;
			goto end;
		}
		//convert dob to correct format
		
		$query="insert into users(userid,type) values('$rollno','student')";
		//echo $query;
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Couldn't insert data in main table";
			goto end;
		}
		$query="insert into students_main(rollno,fullname,dob,emailid,college,branch,count_submissions,count_AC,count_WA,count_TLE,count_RTE) values('$rollno','$fullname','$dob','$emailid','$college','$branch',0,0,0,0,0)";
		//echo $query;
		$res=mysqli_query($db,$query);
		if(!($res))
		{
	
			$status=false;
			$errorstr="Couldn't insert data";
			$errorstr=mysqli_error($db);
			goto end;
		}
		$query="insert into logininfo(username,password,createdon) values('$rollno',SHA1('$password'),CURRENT_TIMESTAMP())";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			//$errorstr=mysqli_error($db);
			$status=false;
			$errorstr="Couldn't create account";
			goto end;
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		header("Location: ./../student_registration.php?prev=done");
	}
	else
	{
		mysqli_rollback($db);		
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../student_registration.php?prev=fail&msg=$errorstr");
	}
	;
}
?>
