<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
include('./includes/mysql_login_view.php');
error_reporting(-1);
$query="select facultyid from faculty_main";
$res=mysqli_query($db,$query);
$facultyid_array=array();
while($r=mysqli_fetch_array($res))
{
	$facultyid_array[]=$r['facultyid'];
}

$edit=false;
$edit_fid='';

if(isset($_GET['edit']))
{
	$edit_fid=mysqli_real_escape_string($db,$_GET['edit']);
	$edit_fid=strtoupper($edit_fid);
	foreach($facultyid_array as $facultyid)
	{
		if($facultyid==$edit_fid)
		{
			$edit=true;
			break;
		}
	}
	if($edit)
	{
	
		$query="select * from faculty_main where facultyid='$edit_fid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$edit=false;
		}
		else
		{
			$res=mysqli_fetch_array($res);
			$facultyid=$res['facultyid'];
			$fullname=$res['fullname'];
			$dob=$res['dob'];
			$emailid=$res['emailid'];
			$college=$res['college'];
			$branch=$res['branch'];
			$designation=$res['designation'];
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
			<h4 style='color: #55bc48; display:inline-block;'> Faculty Registration Successful </h4> 
		
		";
	}
	else if($_GET['prev']=="fail")
	{
		echo "
		
			<img src='./assets/invalid.png'>
			<h4 style='color: #e74833; display:inline-block;'> Faculty Registration Unsuccessful </h4> 
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
<form id='login_form' method='post' action='./scripts/make_newfaculty.php'>
	<h2>New Faculty Registration</h2>
	<table id='logintable' >
		<tr>
 		<td style='text-align:right'>
			<label>Faculty ID :</label>
 		</td>
		<td>
			<?php
				if($edit)
				{
					echo $facultyid;//make better for eyes	
					echo "<input type='hidden' id='facultyid' name='facultyid' value='$facultyid' maxlength='20'>";
				}
				else
				{
  					echo  "<input type='text' id='facultyid' name='facultyid' value='' maxlength='20' required onblur='validate_facultyid(this)'>";
				}
			?>
  			
  			<script language='javascript' type='text/javascript'>
			function validate_facultyid(input)
			{
				input.value = input.value.toUpperCase();
				var facultyid_list = new Array();
				<?php
					$i=0;
					foreach($facultyid_array as $rn)
					{
						echo "facultyid_list[$i]='$rn';
						";
						$i++;
					}
				?>
				
				for(i=0;i<facultyid_list.length;i++)
				{
					if(facultyid_list[i] == input.value)
					{
						input.setCustomValidity('Sorry, this Faculty ID is already taken.');
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
 		<td style='text-align:right'>
			<label>Full Name :</label>
 		</td>
		<td>
  			<input type='text' name='fullname' <?php if($edit){ echo "value='$fullname'";} ?> maxlength='50' required x-moz-errormessage='Enter Faculty Name'>
 		</td>
		</tr>
 
 		<tr>
 		<td style='text-align:right'>
			<label>Email :</label>
 		</td>
		<td>
  			<input type='email' name='emailid' placeholder='xyz@example.com' <?php if($edit){ echo "value='$emailid'";} ?> maxlength='30' required x-moz-errormessage='Enter a valid Email Address'>
 		</td>
		</tr>
		<?php
		if(!$edit)
		{
			echo "
			<tr>
	 			<td style='text-align:right'>
	  				<label>Password :</label>
	 			</td>
	 			<td>
	 				<input type='password' id='password' name='password' value='' pattern='.{6,}' required title='6 characters minimum'>
	 			</td>
			</tr>
		
			<tr>
	 			<td style='text-align:right'>
	  				<label>Confirm Password :</label>
	 			</td>
	 			<td>
	 				<input type='password' id='confirm_password' name='confirmpassword' required x-moz-errormessage='The two passwords must match' oninput='check(this)' onblur='check(this)'>
	 				
						<script language='javascript' type='text/javascript'>
						function check(input) 
						{
						    if (input.value != document.getElementById('password').value) 
						    {
							input.setCustomValidity('The two passwords must match');
						    }
						    else
						    {
							input.setCustomValidity('');
						    }
						}
						</script>
	 			</td>
			</tr>
			";
		}
		?>
		
		<tr>
 		<td style='text-align:right'>
			<label>Date (YYYY-MM-DD) :</label>
 		</td>
		<td>
  			<input type='text' name='dob' <?php if($edit){ echo "value='$dob'";} ?> maxlength='30'>
 		</td>
		
		
		
		<tr>
 		<td style='text-align:right'>
			<label>College :</label>
 		</td>
		<td>
  			<input type='text' name='college' <?php if($edit){ echo "value='$college'";} ?> maxlength='30'>
 		</td>
 		
 		<tr>
 		<td style='text-align:right'>
			<label>Branch :</label>
 		</td>
		<td>
			<input type='text' name='branch' <?php if($edit){ echo "value='$branch'";} ?> maxlength='30'>
			
		</td>
		</tr>
		
		
		<tr>
 		<td style='text-align:right'>
			<label>Designation :</label>
 		</td>
		<td>
  			<input type='text' name='designation' <?php if($edit){ echo "value='$designation'";} ?> maxlength='30'>
 		</td>
 		
		
		
		
		</tr>
		
		<tr>
			<td>
  			
 			</td>
 			<td>
			<?php
				if($edit)
				{
					echo "<input type='hidden' name='edit' value='true'>";
				}
			?>
  			<input type='submit' value='Register' class='bbutton'>
 			</td>
		</tr>
	</table>
</form>
