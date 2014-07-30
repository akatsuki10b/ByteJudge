<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
include('./includes/mysql_login_view.php');
error_reporting(-1);

$query="select rollno from students_main";
$res=mysqli_query($db,$query);
$rollno_array=array();
while($r=mysqli_fetch_array($res))
{
	$rollno_array[]=$r['rollno'];
}


$edit=false;
$edit_rno='';

if(isset($_GET['edit']))
{
	$edit_rno=mysqli_real_escape_string($db,$_GET['edit']);
	$edit_rno=strtoupper($edit_rno);
	foreach($rollno_array as $rollno)
	{
		if($rollno==$edit_rno)
		{
			$edit=true;
			break;
		}
	}
	if($edit)
	{
	
		$query="select * from students_main where rollno='$edit_rno'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$edit=false;
		}
		else
		{
			
			$res=mysqli_fetch_array($res);
			$rollno=$res['rollno'];
			$fullname=$res['fullname'];
			$dob=$res['dob'];
			$emailid=$res['emailid'];
			$college=$res['college'];
			$branch=$res['branch'];
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
			<h4 style='color: #55bc48; display:inline-block;'> Student Registration Successful </h4> 
		
		";
	}
	else if($_GET['prev']=="fail")
	{
		echo "
		
			<img src='./assets/invalid.png'>
			<h4 style='color: #e74833; display:inline-block;'> Student Registration Unsuccessful </h4> 
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
<form id='login_form'  action='./scripts/make_newstudent.php' method='post'>
	<h2>New Student Registration</h2>
	<table id='logintable' >
		<tr>
 		<td style='text-align:right'>
			<label>Username :</label>
 		</td>
		<td>
			<?php
				if($edit)
				{
					echo $rollno;//make better for eyes	
					echo "<input type='hidden' id='username' name='rollno' value='$rollno' maxlength='20'>";
				}
				else
				{
  					echo  "<input type='text' id='username' name='rollno' value='' maxlength='20' required onblur='validate_username(this)'>";
				}
			?>
  			<script language='javascript' type='text/javascript'>
			function validate_username(input)
			{
				input.value = input.value.toUpperCase();
				var rollno_list = new Array();
				<?php
					$i=0;
					foreach($rollno_array as $rn)
					{
						echo "rollno_list[$i]='$rn';
						";
						$i++;
					}
				?>
				for(i=0;i<rollno_list.length;i++)
				{
					if(rollno_list[i] == input.value)
					{
						input.setCustomValidity('Sorry, this username is already taken.');
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
  			<input type='text' name='fullname' maxlength='50' <?php if($edit){ echo "value='$fullname'";} ?> required x-moz-errormessage='Enter Student Name'>
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
			<!--
  			<select name='branch' <?php if($edit){ echo "value='$branch'";} ?>>
				<option value='cs'>Computer</option>
				<option value='ece'>Electronics</option>
				<option value='ee'>Electrical</option>
				<option value='civil'>Civil</option>
				<option value='chem'>Chemical</option>
				<option value='mech'>Mechanical</option>
			</select>
			-->
 		</td>
		</tr>
		
		
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
