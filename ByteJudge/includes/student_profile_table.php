<?php

$errorstr='';
$status=true;
if(!(isset($_GET['rollno'])))
{
	$errorstr="No Rollno provided";
	$status=false;
	goto end;
}

include('./includes/mysql_login_view.php');
$rollno='';
$problemssolved=array();
$groupsbelongto=array();

$rollno=strip_tags(mysqli_real_escape_string($db,$_GET['rollno']));
$rollno=strtoupper($rollno);
$query="select * from students_main where rollno='$rollno'";
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
	$errorstr="No student by rollno $rollno";
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
$count_AC=$res['count_AC'];
$count_RTE=$res['count_RTE'];
$count_TLE=$res['count_TLE'];
$count_WA=$res['count_WA'];
$count_CTE=$res['count_CTE'];

$query="select groupid from students_belongtogroups where rollno='$rollno'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Cannot fetch student groups";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$groupsbelongto[]=$r['groupid'];
}

$query="select distinct(problem_code) from submissions where username='$rollno' and verdict='AC'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Couldn't fetch successful submissions";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$problemssolved[]=$r['problem_code'];
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
				Roll No. :
	 		</td>
			<td class="right">
	  			<a href='#'><?php echo "$rollno"; ?></a>
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
				Groups :
	 		</td>
			<td class="right">
				<?php
					foreach($groupsbelongto as $val=>$groupid)
					{
						echo "  <a href='./studentgroup_profile.php?groupid=$groupid'>$groupid</a>";
						if(isset($groupsbelongto[$val+1]))
							echo ",";
						
					}
				?>
	  			
	 		</td>
		</tr>
		
		<tr>
	 		<td class="left">
				Solved Problems :
	 		</td>
			<td class="right">
				<?php
					foreach($problemssolved as $val=>$problemcode)
					{
						echo "<a href='./viewproblem.php?pcode=$problemcode'>  $problemcode";
						if(isset($problemssolved[$val+1]))
							echo ",";
						echo "  </a>";
					}
				?>
	  			
	 		</td>
		</tr>
	</tbody>
</table>
<div id='profile'>
<h3>Submission Statistics</h3>
</div>
<table id='tc_analysis_table'>
	
	<tr>
		<td><img src='./assets/AC.png' title='Accepted Submissions'></td>
		<td><img src='./assets/WA.png' title='Wrong Submissions'></td>
		<td><img src='./assets/TLE.png' title='Time Limit Exceeded'></td>
		<td><img src='./assets/RTE.png' title='Run Time Error'></td>
		<td><img src='./assets/CTE.png' title='Compile Time Error'></td>
	</tr>
	<tr>
		<td><?php echo $count_AC; ?></td>
		<td><?php echo $count_WA; ?></td>
		<td><?php echo $count_TLE; ?></td>
		<td><?php echo $count_RTE; ?></td>
		<td><?php echo $count_CTE; ?></td>
	</tr>
</table>
<table>
		
		<tr>	
			<?php
				if($_SESSION['type']=="admin" || ($_SESSION['type']=="student" && $_SESSION['username']==$rollno))
				{ 
					echo "
						<td>
							<a href='./student_registration.php?edit=$rollno'>
				
					  			<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Edit Profile'/>
								</a>
				
				 		</td>
						<td>
							<a href='./change_password.php?user=$rollno'>
	  						 <input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Change Password'/>
							</a>
	 					</td>
					";
				}
				if($_SESSION['type']=="admin")
				{
					echo "
				 		<td>
							<form action='./scripts/deletestudents.php' method='post'>
							<input type='hidden' name='studentstodelete[]' value='$rollno'>
				  			 <input type='submit' id='buttonblue' name='buttonblue' class='rbutton' value='Delete Profile'/>
							</form>
				 		</td>
	 		
					";
				}
				echo "<td><a href='./user_submissions.php?username=$rollno'><input type='button' value='Submissions' class='bbutton' ></a></td>";
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
