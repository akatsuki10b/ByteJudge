<?php
require('./determineidentityfunc.php');
if(!(isloggedin("faculty")==true || isloggedin("admin")==true))
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
	
	$studentgroupstodelete=array();
	if(isset($_POST['studentgroupstodelete']))
	{
		$studentgroupstodelete=$_POST['studentgroupstodelete'];
	}
	
	$query="delete from groups_students where groupid=";	
	foreach($studentgroupstodelete as $groupid)
	{
		$groupid=strip_tags(mysqli_real_escape_string($db,$groupid));
		$res=mysqli_query($db,$query."'$groupid'");
	//	echo $query.$groupid;
		if(!($res))
		{
			$status=false;
			$errorstr="Error in delete query";
			goto end;
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		header("Location: ./../view_studentgroups.php?prev=done&msg=Student groups deleted successfully");
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../view_studentgroups.php?prev=fail&error=Couldn't delete student groups: $errorstr");
	}
	;
}
?>
