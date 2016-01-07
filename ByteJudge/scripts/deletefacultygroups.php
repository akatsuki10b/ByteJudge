<?php
require('./determineidentityfunc.php');
if(!( isloggedin("admin")==true))
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
	
	$facultygroupstodelete=array();
	if(isset($_POST['facultygroupstodelete']))
	{
		$facultygroupstodelete=$_POST['facultygroupstodelete'];
	}
	
	$query="delete from groups_faculty where groupid=";	
	foreach($facultygroupstodelete as $groupid)
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
		header("Location: ./../view_facultygroups.php?prev=done&msg=faculty groups deleted successfully");
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../view_facultygroups.php?prev=fail&error=Couldn't delete faculty groups: $errorstr");
	}
	;
}
?>
