<?php
require('./determineidentityfunc.php');
if(!(isloggedin("faculty")==true))
{
	header('Location: ./../home.php');
}
else
{	
	$status=true;
	$errorstr='';
	$testid='';
	if(!isset($_POST['testid']))
	{
		$errorstr="No testid provided";
		$status=false;
		goto end;
	}
	else
	{	
		include('./../includes/mysql_login_faculty.php');
		$testid=strip_tags(mysqli_real_escape_string($db,$_POST['testid']));

		$query="delete from test_submissions where testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Error in deleting submissions";
			$errorstr=mysqli_error($db);
			goto end;
		}
		$query="delete from test_attemptedby where testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Error in deleting Attempted by record";
			goto end;
		}
		
		$query="update test_problems set totalsubmissions=0,acceptedsubmissions=0, solvedby=0 where testid='$testid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Error in deleting submission counts";
			goto end;
		}
		end:
	
		if($status)
		{
			mysqli_commit($db);
			mysqli_close($db);
			header("Location: ./../view_test.php?testid=$testid&prev=done&msg=Stats reset successfully");
		}
		else
		{
			
			//echo mysqli_error($db);
			mysqli_rollback($db);
			mysqli_close($db);
			header("Location: ./../view_test.php?testid=$testid&prev=fail&error=$errorstr");
		}
	}
	
}
?>
