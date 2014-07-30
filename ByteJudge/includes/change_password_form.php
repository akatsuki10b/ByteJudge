<?php
$status=true;
$errorstr='';
if(isset($_GET['user']))
{
	
	$username=$_GET['user'];
	include('./includes/mysql_login_password.php');
	$username=strip_tags(mysqli_real_escape_string($db,$username));
	$query="select userid from users where userid='$username'";
	$type='';
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$status=false;
		$errorstr="Error in prequery";
		goto end;
	}
	$r=mysqli_num_rows($res);
	if($r!=1)
	{
		$status=false;
		$errorstr="No user by username $username exists";
		goto end;
	}
	if($_SESSION['type']=="admin" || ($_SESSION['username']==$username))
	{
		echo "
	<form id='login_form' method='post' action='./scripts/changepassword.php'>
		<h2>Change Password</h2>
		<input type='hidden' name='username' value='$username'>
		<table id='logintable' >
			<tr>
	 			<td style='text-align:right'>
	  				<label>New Password :</label>
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
		
			<tr>
				<td>
	  			
	 			</td>
	 			<td>
	  			<input type='submit' value='Change Password' class='bbutton'>
	 			</td>
			</tr>
		</table>
	</form>
	";
	}
	else
	{
		$status=false;
		$errorstr="You are not authorised to access this page";
		goto end;
	}
	end:
	if(!($status))
	{
		echo $errorstr;
	}
}
else
{
	echo "No username provided";
}
?>
