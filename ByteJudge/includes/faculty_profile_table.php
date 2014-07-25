<?php

$errorstr='';
$status=true;
if(!(isset($_GET['facultyid'])))
{
	$errorstr="No Facultyid provided";
	$status=false;
	goto end;
}

include('./includes/mysql_login_view.php');
$facultyid='';
$problemsmade=array();
$groupsbelongto=array();

$facultyid=strip_tags(mysqli_real_escape_string($db,$_GET['facultyid']));
$facultyid=strtoupper($facultyid);
$query="select * from faculty_main where facultyid='$facultyid'";
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
	$errorstr="No Faculty with ID $facultyid";
	goto end;
}
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$res=mysqli_fetch_array($res);
$fullname=$res['fullname'];
$dob=$res['dob'];
$emailid=$res['emailid'];
$college=$res['college'];
$branch=$res['branch'];
$designation=$res['designation'];
$count_problemsadded=$res['count_problemsadded'];

$query="select groupid from faculty_belongtogroups where facultyid='$facultyid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Cannot fetch faculty groups";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$groupsbelongto[]=$r['groupid'];
}

$query="select distinct(problem_code) from problems where addedby='$facultyid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Couldn't fetch problems authored";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$problemsmade[]=$r['problem_code'];
}
?>
<div id='profile'>
<h2><?php echo "$fullname's"; ?> Profile</h2>
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
				Facultyid :
	 		</td>
			<td class="right">
	  			<a href='#'><?php echo "$facultyid"; ?></a>
	 		</td>
		</tr>
	
		<tr>
	 		<td class="left">
				Name :
	 		</td>
			<td class="right">
	  			<?php echo "$fullname"; ?>
	 		</td>
		</tr>
		<tr>
	 		<td class="left">
				College :
	 		</td>
			<td class="right">
	  			<?php echo "$college"; ?>
	 		</td>
		</tr>
		<tr>
	 		<td class="left">
				<label> Branch :</label>
	 		</td>
			<td class="right">
	  			<?php echo "$branch"; ?>
	 		</td>
		</tr>
		
		
		<tr>
	 		<td class="left">
				Designation :
	 		</td>
			<td class="right">
	  			<?php echo "$designation"; ?>
	 		</td>
		</tr>
		<tr>
	 		<td class="left">
				Number of problems added :
	 		</td>
			<td class="right">
	  			<?php echo "$count_problemsadded"; ?>
	 		</td>
		</tr>
		<tr>
	 		<td class="left">
				Groups :
	 		</td>
			<td class="right">
				<?php
					foreach($groupsbelongto as $val=>$groupid)
					{
						echo "  <a href='./facultygroup_profile.php?groupid=$groupid'>$groupid</a>";
						if(isset($groupsbelongto[$val+1]))
							echo ",";
						
					}
				?>
	  			
	 		</td>
		</tr>
		
		<tr>
	 		<td class="left">
				Added Problems :
	 		</td>
			<td class="right">
				<?php
					foreach($problemsmade as $val=>$problemcode)
					{
						echo "<a href='./viewproblem.php?pcode=$problemcode'>  $problemcode";
						if(isset($problemsmade[$val+1]))
							echo ",";
						echo "  </a>";
					}
				?>
	  			
	 		</td>
		</tr>
	</tbody>
</table>
<table>
		
		<tr>	
			<?php
				if($_SESSION['type']=="admin" || ($_SESSION['type']=="faculty" && $_SESSION['username']==$facultyid))
				{ 
					echo "
						<td>
							<a href='./faculty_registration.php?edit=$facultyid'>
				
					  			<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Edit Profile'/>
								</a>
				
				 		</td>
						<td>
							<a href='./change_password.php?user=$facultyid'>
	  						 <input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Change Password'/>
							</a>
	 					</td>
					";
				}
				if($_SESSION['type']=="admin")
				{
					echo "
				 		<td>
							<form action='./scripts/deletefaculty.php' method='post'>
							<input type='hidden' name='facultytodelete[]' value='$facultyid'>
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
