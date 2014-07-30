<?php
require('./determineidentityfunc.php');
if(!(isloggedin("admin")==true || (  isloggedin("faculty")==true && isset($_POST['edit']) && isset($_POST['facultyid']) && $_SESSION['username']==strtoupper($_POST['facultyid']) )  ))
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
	$facultyid='';
	$fullname='';
	$dob='';
	$emailid='';
	$college='';
	$branch='';
	$designation='';
	$errorstr='';
	$password='';
	$confirmpassword='';
	$status=true;

	$edit=false;
	if(isset($_POST['edit']))
	{
		
		$edit=true;
	}
	if(isset($_POST['facultyid']))
		$facultyid=strip_tags(mysqli_real_escape_string($db,$_POST['facultyid']));
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
	if(isset($_POST['designation']))
		$designation=strip_tags(mysqli_real_escape_string($db,$_POST['designation']));
	if(isset($_POST['confirmpassword']))
		$confirmpassword=strip_tags(mysqli_real_escape_string($db,$_POST['confirmpassword']));

	$facultyid=strtoupper($facultyid);
	if($facultyid=='' || $fullname=='' || ($password=='' && $edit==false))
	{
		$errorstr="not enough details to create new faculty";
		$status=false;
		goto end;
	}

	$query="select userid from users where userid='$facultyid'";
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
			$errorstr="Faculty with facultyid $facultyid doesn't exist";
			$status=false;
			goto end;
		}
		
		$query="select facultyid from faculty_main where facultyid='$facultyid'";
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
			$errorstr="Faculty with facultyid $facultyid doesn't exist";
			$status=false;
			goto end;
		}

		$query="update faculty_main set fullname='$fullname',dob='$dob',emailid='$emailid',college='$college', branch='$branch',designation='$designation' where facultyid='$facultyid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Couldn't update details of faculty $facultyid";
			$errorstr=mysqli_error($db);
			$status=false;
			goto end;
		}
	}
	else
	{
		if($res>0)
		{	
			$errorstr= "user with ID $facultyid already exists";
			$status=false;
			goto end;
		}
		//convert dob to correct format
		if($password!=$confirmpassword)
		{
			$errorstr="passwords do not match";
			$status=false;
			goto end;
		}
		$query="insert into users(userid,type) values('$facultyid','faculty')";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Couldn't insert data in main table";
			goto end;
		}
		$query="insert into faculty_main(facultyid,fullname,dob,emailid,college,branch,designation,count_problemsadded) values('$facultyid','$fullname','$dob','$emailid','$college','$branch','$designation',0)";
		//echo $query;
		$res=mysqli_query($db,$query);
		if(!($res))
		{
	
			$status=false;
	
			$errorstr="Couldn't insert data";
			//$errorstr=mysqli_error($db);
			goto end;
		}
		$query="insert into logininfo(username,password,createdon) values('$facultyid',SHA1('$password'),CURRENT_TIMESTAMP())";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
	
			$status=false;
			$errorstr="Couldn't create account";
			$errorstr=mysqli_error($db);
			goto end;
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		header("Location: ./../faculty_registration.php?prev=done");
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../faculty_registration.php?prev=fail&msg=$errorstr");
	}
	
}
?>
