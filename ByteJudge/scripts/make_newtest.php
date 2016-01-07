<?php
require('./determineidentityfunc.php');
if(!(isloggedin("faculty")==true))
{
	header('Location: ./../home.php');
}
else
{
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	include('./../includes/mysql_login_faculty.php');
	error_reporting(-1);
	mysqli_autocommit($db,false);

	$testid='';
	$testname='';
	$testdetails='';
	$createdby=$_SESSION['username'];
	$visiblefrom='';
	$visibletill='';
	$problems=array();
	$studentgroups_visibleto=array();
	
	$from_year='';
	$from_month='';
	$from_day='';
	$from_hour='';
	$from_minute='';
	$from_second='';
	
	$till_year='';
	$till_month='';
	$till_day='';
	$till_hour='';
	$till_minute='';
	$till_second='';


	$errorstr='';
	$status=true;
	$errorstr_2='Couldn\'t add problems : ';//for problems
	$status_2=true;
	$errorstr_3='Couldn\'t add student groups  : ';//for studentgroups
	$status_3=true;	

	$edit=false;
	if(isset($_POST['edit']))
	{
		$edit=true;
	}

	if(isset($_POST['testid']))
		$testid=strip_tags(mysqli_real_escape_string($db,$_POST['testid']));
	if(isset($_POST['testname']))
		$testname=strip_tags(mysqli_real_escape_string($db,$_POST['testname']));
	if(isset($_POST['testdetails']))
		$testdetails=strip_tags(mysqli_real_escape_string($db,$_POST['testdetails']));
	/*if(isset($_POST['visiblefrom']))
		$visibleon=strip_tags(mysqli_real_escape_string($db,$_POST['visibleon']));
	if(isset($_POST['visibletill']))
		$visibletill=strip_tags(mysqli_real_escape_string($db,$_POST['visibletill']));*/
	if(isset($_POST['problemstoadd']))
		$problems=$_POST['problemstoadd'];
	if(isset($_POST['studentgroupstoadd']))
		$studentgroups_visibleto=$_POST['studentgroupstoadd'];

	if(isset($_POST['from_year']))
		$from_year=$_POST['from_year'];
	if(isset($_POST['from_month']))
		$from_month=$_POST['from_month'];
	if(isset($_POST['from_day']))
		$from_day=$_POST['from_day'];
	if(isset($_POST['from_hour']))
		$from_hour=$_POST['from_hour'];
	if(isset($_POST['from_minute']))
		$from_minute=$_POST['from_minute'];
	if(isset($_POST['from_second']))
		$from_second=$_POST['from_second'];

	if(isset($_POST['till_year']))
		$till_year=$_POST['till_year'];
	if(isset($_POST['till_month']))
		$till_month=$_POST['till_month'];
	if(isset($_POST['till_day']))
		$till_day=$_POST['till_day'];
	if(isset($_POST['till_hour']))
		$till_hour=$_POST['till_hour'];
	if(isset($_POST['till_minute']))
		$till_minute=$_POST['till_minute'];
	if(isset($_POST['till_second']))
		$till_second=$_POST['till_second'];

	$testid=strtoupper($testid);
	if($testid=='' || $testname=='')
	{
		$errorstr="not enough details to create new test";
		$status=false;
		goto end;
	}
	
	if($from_year=='' || $from_month=='' || $from_day=='' || $from_hour=='' || $from_minute=='' || $from_second=='')
	{
		$errorstr="VisibleFrom timestamp not complete";
		$status=false;	
		goto end;
	}
	
	$visiblefrom=$from_year."-".$from_month."-".$from_day." ".$from_hour.":".$from_minute.":".$from_second;
	if($till_year=='' || $till_month=='' || $till_day=='' || $till_hour=='' || $till_minute=='' || $till_second=='')
	{
		$errorstr="VisibleTill timestamp not complete";
		$status=false;	
		goto end;
	}
	$visibletill=$till_year."-".$till_month."-".$till_day." ".$till_hour.":".$till_minute.":".$till_second;
	echo "$visiblefrom $visibletill";

	

	$query="select testid from test_main where testid='$testid'";
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
			$errorstr="Test with ID $testid doesn't exist";
			$status=false;
			goto end;
		}
		
		

		$query="update test_main set testname='$testname',testdetails='$testdetails',visiblefrom='$visiblefrom',visibletill='$visibletill' where testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Couldn't update details of test $testid";
			$errorstr=mysqli_error($db);
			$status=false;
			goto end;
		}
		$query="delete from test_problems where testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Error in deleting previous problems ";	
		//	echo mysqli_error($db);
			$status=false;
			goto end;
		}
		$query="delete from test_visibleto where testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Error in deleting previous student groups ";	
			$status=false;
			goto end;
		}
	}	
	else
	{
		if($res>0)
		{	
			$errorstr= "test with ID $testid already exists";
			$status=false;
			goto end;
		}
	
		$query="insert into test_main(testid,testname,testdetails,createdby,createdon,visiblefrom,visibletill) values('$testid','$testname','$testdetails','$createdby',CURRENT_TIMESTAMP(),'$visiblefrom','$visibletill')";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Couldn't insert data of test in Database";
			$status=false;
			goto end;
		}
	
		
	}
	$query="insert into test_problems(testid, problem_code) values('$testid',";
	foreach($problems as $probcode)
	{
		$probcode=strip_tags(mysqli_real_escape_string($db,$probcode));
		$res=mysqli_query($db,$query."'$probcode')");
		if(!($res))
		{
			$status_2=false;
			$errorstr_2.="[$testid - $probcode]";
		}
	}

	$query="insert into test_visibleto(testid,groupid) values('$testid',";
	foreach($studentgroups_visibleto as $groupid)	
	{
		$groupid=strip_tags(mysqli_real_escape_string($db,$groupid));
		
		$res=mysqli_query($db,$query."'$groupid')");
		//echo $query."'$groupid'";
		if(!($res))
		{
			$status_3=false;
			$errorstr_3.="[$testid - $groupid]";
			
		}
	}
	end:
	echo "$errorstr_2 $errorstr_3";
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		if($status_2==false || $status_3==false)
		{
			header("Location: ./../new_test.php?prev=partial&partialfailmsg=$errorstr_2<br> $errorstr_3");
		}
		else
		{
			header("Location: ./../new_test.php?prev=done");
		}
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../new_test.php?prev=fail&msg=$errorstr");
	}
	;
}
?>
