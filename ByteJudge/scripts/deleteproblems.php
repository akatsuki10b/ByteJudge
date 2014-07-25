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

	$errorstr="";
	$status=true;
	
	$problemstodelete=array();
	if(isset($_POST['problemstodelete']))
	{
		$problemstodelete=$_POST['problemstodelete'];
	}
	
	$query="delete from problems where problem_code=";	
	echo "len is ";
	echo sizeof($problemstodelete);
	foreach($problemstodelete as $problem_code)
	{
		
		$problem_code=strip_tags(mysqli_real_escape_string($db,$problem_code));
		$res=mysqli_query($db,$query."'$problem_code'");
		echo $query."'$problem_code'";
	//	echo $query.$problem_code;
		if(!($res))
		{
			$status=false;
			
			$errorstr="Error in delete query";
			$errorstr=mysqli_error($db);
			goto end;
		}
		$res2=shell_exec("rm -rf ./../PROBLEMS/$problem_code");
		//echo "rm -rf ./../PROBLEMS/$problem_code";
		$res3=shell_exec("rm -rf ./../submissions/*/$problem_code");
		//echo "rm -rf ./../submissions/*/$problem_code";
		if($res2!="" || $res3!="")
		{
			$status=false;
			$errorstr="Error in deleting files for $problem_code";
			goto end;//it may just be that the problem was not in the database..no need of goto end maybes
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);

		mysqli_close($db);
	
		header("Location: ./../view_allproblems.php?prev=done&msg=problem deleted successfully");
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
		echo "$errorstr";
		header("Location: ./../view_allproblems.php?prev=fail&error=Couldn't delete problems: $errorstr");
	}
	;
}
?>
