<?php
require('./determineidentityfunc.php');
if(!(isloggedin("admin")==true))
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

	$errorstr="";
	$status=true;
	
	$facultytodelete=array();
	if(isset($_POST['facultytodelete']))
	{
		$facultytodelete=$_POST['facultytodelete'];
	}
	
	$query="delete from users where userid=";	
	foreach($facultytodelete as $facultyid)
	{
		$facultyid=strip_tags(mysqli_real_escape_string($db,$facultyid));
		$res=mysqli_query($db,$query."'$facultyid'");
		echo $query.$facultyid;
		if(!($res))
		{
			$status=false;
			$errorstr="Error in delete query";
			goto end;
		}
		$res2=shell_exec("rm -rf ./../submissions/$facultyid");
		
		if($res2)
		{
			$errorstr="Error in deleting files for $facultyid";
			goto end;//it may just be that the user was not in the database..no need of goto end maybes
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		header("Location: ./../view_faculty.php?prev=done&msg=faculty deleted successfully");
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../view_faculty.php?prev=fail&error=Couldn't delete faculty: $errorstr");
	}
	;
}
?>
