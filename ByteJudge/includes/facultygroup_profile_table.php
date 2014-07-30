<?php

$errorstr='';
$status=true;
if(!(isset($_GET['groupid'])))
{
	$errorstr="No Group ID provided";
	$status=false;
	goto end;
}

include('./includes/mysql_login_view.php');
$groupid='';
$facultypartof=array();

$groupid=strip_tags(mysqli_real_escape_string($db,$_GET['groupid']));
$groupid=strtoupper($groupid);
$query="select * from groups_faculty where groupid='$groupid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$status=false;
	$errorstr="Error in accessing Database";
	goto end;
}
if(mysqli_num_rows($res)!=1)
{
	$status=false;
	$errorstr="No group by ID $groupid";
	goto end;
}
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$res=mysqli_fetch_array($res);
$groupname=$res['groupname'];
$groupdetails=$res['groupdetails'];

$query="select facultyid from faculty_belongtogroups where groupid='$groupid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Cannot fetch group faculty";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$facultypartof[]=$r['facultyid'];
}

?>
<div id='profile'>
<h2><?php echo "$groupname's"; ?> Profile</h2>
</div>
<table class='profile_table' >
	<thead>
    	<tr>
     	 	<th class="left"></th>
      		<th class="right"></th>
    	</tr>
  	</thead>
  	<tbody>
		<tr>
	 		<td class="left">
				Faculty Group ID :
	 		</td>
			<td class="right">
	  			<a href='#'><?php echo "$groupid"; ?></a>
	 		</td>
		</tr>
	
		<tr>
	 		<td class="left">
				Group Name :
	 		</td>
			<td class="right">
	  			<?php echo "$groupname"; ?>
	 		</td>
		</tr>
		<tr>
	 		<td class="left">
				Group details :
	 		</td>
			<td class="right">
	  			<?php echo "$groupdetails"; ?>
	 		</td>
		</tr>
		
		
		
		<tr>
	 		<td class="left">
				Faculty :
	 		</td>
			<td class="right">
				<?php
					foreach($facultypartof as $val=>$facultyid)
					{
						echo "  <a href='./faculty_profile.php?facultyid=$facultyid'>$facultyid</a>";
						if(isset($facultypartof[$val+1]))
							echo ",";
						
					}
				?>
	  			
	 		</td>
		</tr>
		
		
	</tbody>
</table>
<table>
		
		<tr>	
			<?php
				if($_SESSION['type']=="admin")
				{ 
					echo "
						<td>
							<a href='./new_faculty_group.php?edit=$groupid'>
				
					  			<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Edit Profile'/>
								</a>
				
				 		</td>
				
				 		<td>
							<form action='./scripts/deletefacultygroups.php' method='post'>
							<input type='hidden' name='facultygroupstodelete[]' value='$groupid'>
				  			 <input type='submit' id='buttonblue' name='buttonblue' class='rbutton' value='Delete Profile'/>
							</form>
				 		</td>
	 		
					";
				}
				?>
				
		</tr>
	</tbody>
</table>

<?php
end:
if(!$status)
{
	echo $errorstr;
}
?>
