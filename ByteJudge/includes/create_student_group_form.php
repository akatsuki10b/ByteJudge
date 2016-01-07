
<h3>Create New Student Group</h3>
<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
include('./includes/mysql_login_view.php');
error_reporting(-1);

$query="select groupid from groups_students";
$res=mysqli_query($db,$query);
$studentgroupid_array=array();
while($r=mysqli_fetch_array($res))
{
	$studentgroupid_array[]=$r['groupid'];
}


$edit=false;
$edit_gid='';

if(isset($_GET['edit']))
{
	$edit_gid=mysqli_real_escape_string($db,$_GET['edit']);
	$edit_gid=strtoupper($edit_gid);
	foreach($studentgroupid_array as $groupid)
	{
		if($groupid==$edit_gid)
		{
			$edit=true;
			break;
		}
	}
	if($edit)
	{
	
		$query="select * from groups_students where groupid='$edit_gid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$edit=false;
		}
		else
		{
			$res=mysqli_fetch_array($res);
			$groupid=$res['groupid'];
			$groupname=$res['groupname'];
			$groupdetails=$res['groupdetails'];
		
			$query="select rollno from students_belongtogroups where groupid='$edit_gid'";
			$res=mysqli_query($db,$query);
			if(!($res))
			{
				$edit=false;
			}
			else
			{
				$studentsingroup=array();
				while($r=mysqli_fetch_array($res))
				{
					$studentsingroup[]=$r['rollno'];
				}
			}
		}
	}

}

if(isset($_GET['prev']))
{
	echo "<div style='text-align: center;' >";
	if($_GET['prev']=="done")
	{
		echo "
		
			<img src='./assets/valid.png'>
			<h4 style='color: #55bc48; display:inline-block;'> Student Group Registration Successful </h4> 
		
		";
	}
	else if($_GET['prev']=="partial")
	{
		echo "
		
			<img src='./assets/valid.png'>
			<h4 style='color: #55bc48; display:inline-block;'> Student Group Registration Partially Successful </h4> 
		";
		if(isset($_GET['partialfailmsg']))
		{
			$partialfailmsg=$_GET['partialfailmsg'];
			echo "<p style='color: #8F8F8F;'> $partialfailmsg </p>";
		}
	}
	else if($_GET['prev']=="fail")
	{
		echo "
	
			<img src='./assets/invalid.png'>
			<h4 style='color: #e74833; display:inline-block;'> Student Group Registration Unsuccessful </h4> 
		";
	}
	$msg="";
	if(isset($_GET['msg']))
	{
		$msg=$_GET['msg'];
		echo "<p style='color: #8F8F8F;'> $msg </p>";
	}
	echo "</div>";
}
?>

<form  method='post'  action='./scripts/make_newstudentgroup.php'  name='new_group'>
<table id='nptable'>
	<tr>
	 	<td>
			<label><strong>Student Group ID :</strong></label>
	 	</td>
		<td>
			<?php
				if($edit)
				{
					echo $groupid;//make better for eyes	
					echo "<input  type='hidden' style='width: 200px;' name='groupid' value='$groupid'>";
				}
				else
				{
  					echo  "<input  type='text' style='width: 200px;' name='groupid' required onblur='validate_groupid(this)'>";
				}
			?>
	  		<script language='javascript' type='text/javascript'>
			function validate_groupid(input)
			{
				input.value = input.value.toUpperCase();
				var studentgroupid_list = new Array();
				<?php
					$i=0;
					foreach($studentgroupid_array as $rn)
					{
						echo "studentgroupid_list[$i]='$rn';
						";
						$i++;
					}
				?>
				
				for(i=0;i<studentgroupid_list.length;i++)
				{
					if(studentgroupid_list[i] == input.value)
					{
						input.setCustomValidity('Sorry, this Student Group ID is already taken.');
						return false;
					}
				}
				input.setCustomValidity('');
				return true;
			}
			</script>
	 	</td>
	</tr>
	
	<tr>
 		<td valign='top'>
			<label><strong>Student Group Name :</strong></label>
 		</td>
		<td>
  			<input  type='text' style='width: 300px;' name='groupname' <?php if($edit){ echo "value='$groupname'";} ?> required x-moz-errormessage='Enter Student Group Name'>
 		</td>
	</tr>
 
	<tr>
 		<td valign='top'>
  			<label><strong>Student Group Description :</strong></label>
 		</td>
 		<td>
 			<textarea  name='groupdetails' cols='50' rows='5'><?php if($edit){ echo $groupdetails; } ?></textarea>
 		</td>
	</tr>
	
	<?php include("./includes/select_studentgroup_form.php"); ?>
	
	<tr>
		<td>
  			
 		</td>
 		<td>
			<?php
				if($edit)
					echo "<input type='hidden' name='edit' value='$groupid'>";
			?>
  			<input type='submit' value='Create Group' class='bbutton'>
 		</td>
	</tr>
</table>
</form>
