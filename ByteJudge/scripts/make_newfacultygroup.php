<?php
require('./determineidentityfunc.php');
if(!(isloggedin("admin")==true))
{
	header('Location: ./../home.php');
}
else
{
	//apply javascript to ensure all fields are there
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	include('./../includes/mysql_login_admin.php');
	error_reporting(-1);
	mysqli_autocommit($db,false);
	$groupid='';
	$groupname='';
	$groupdetails='';
	$facultytoadd=array();

	$errorstr='';
	$status=true;
	$errorstr_2='Couldn\t add faculty :  ';
	$status_2=true;
	
	$edit=false;
	if(isset($_POST['edit']))
	{
		$edit=true;
	}

	if(isset($_POST['groupid']))
		$groupid=strip_tags(mysqli_real_escape_string($db,$_POST['groupid']));
	if(isset($_POST['groupname']))
		$groupname=strip_tags(mysqli_real_escape_string($db,$_POST['groupname']));
	if(isset($_POST['groupdetails']))
		$groupdetails=strip_tags(mysqli_real_escape_string($db,$_POST['groupdetails']));
	if(isset($_POST['facultytoadd']))
		$facultytoadd=$_POST['facultytoadd'];
	
	$groupid=strtoupper($groupid);
	if($groupid=='' || $groupname=='')
	{
		$errorstr="not enough details to create new faculty group";
		$status=false;
		goto end;
	}

	$query="select groupid from groups_faculty where groupid='$groupid'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$errorstr= "error in prequery";
		//$errorstr=mysqli_error($db);
		$status=false;
		goto end;
	}
	$res=mysqli_num_rows($res);
	if($edit)
	{
		
		if($res!=1)
		{
			$errorstr="Faculty group with groupid $groupid doesn't exist";
			$status=false;
			goto end;
		}
		

		$query="update groups_faculty set groupname='$groupname',groupdetails='$groupdetails' where groupid='$groupid'";
		
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Couldn't update details of faculty group $groupid";
			$errorstr.=mysqli_error($db);
			$status=false;
			goto end;
		}
		$query="delete from faculty_belongtogroups where groupid='$groupid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr="Error in deleting previous members ";	
			$status=false;
			goto end;
		}
	}
	else
	{
		if($res>0)
		{	
			$errorstr= "group by groupid $groupid already exists";
			$status=false;
			goto end;
		}

		$query="insert into groups_faculty(groupid,groupname,groupdetails) values('$groupid','$groupname','$groupdetails')";
		//echo $query;
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			//$errorstr=mysqli_error($db);
			$status=false;
			$errorstr="Couldn't insert data";
			goto end;
		}
	}
	//adding faculty to group
	foreach($facultytoadd as $facultyid)
	{
		$facultyid=strip_tags(mysqli_real_escape_string($db,$facultyid));
		$query="insert into faculty_belongtogroups (facultyid,groupid) values ('$facultyid','$groupid')";
		//echo $query;
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status_2=false;
			$errorstr_2.="[$facultyid - $groupid]";
			
		}
	}
	
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
	
		if($status_2==false)
		{
			header("Location: ./../new_faculty_group.php?prev=partial&partialfailmsg=$errorstr_2");	
		}
		else
		{
			header("Location: ./../new_faculty_group.php?prev=done");
		}
	}
	else
	{
		mysqli_rollback($db);
		mysqli_close($db);
		//echo "$errorstr";
		//echo mysqli_error($db);
		header("Location: ./../new_faculty_group.php?prev=fail&msg=$errorstr");
	}
	;
}
?>
