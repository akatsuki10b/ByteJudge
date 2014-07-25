<?php
if(isset($_GET['loggedout']))
{
	$loggedout=$_GET['loggedout'];
	if($loggedout=="true")
	{
		echo "<div style='text-align: center;' >
		<img src='./assets/valid.png'>
		<h4 style='color: #55bc48; display:inline-block;'> Successfully Logged Out </h4> 
		</div>";
	}
}
else if(isset($_GET['loggedin']))
{
	$loggedin=$_GET['loggedin'];
	$error='';
	if(isset($_GET['error']))
		$error=$_GET['error'];
	if($loggedin=="true")
	{
		echo "<div style='text-align: center;' >
		<img src='./assets/valid.png'>
		<h4 style='color: #55bc48; display:inline-block;'> Successfully Logged In </h4> 
		</div>";
	}
	else if($loggedin=="false")
	{
		echo "<div style='text-align: center;' >
		<img src='./assets/invalid.png'>
			<h4 style='color: #e74833; display:inline-block;'> Log In Unsuccessful </h4> 
			<p style='color: #8F8F8F;'>$error</p>
		</div>";
	}
}

?>
<form id='login_form' method='post' action='./scripts/login_validate.php'>
	<h2>LOGIN</h2>
	<table id='logintable' >
		<tr>
 		<td style='text-align:center; width:40%;'>
			<label>Username :</label>
 		</td>
		<td>
  			<input type='text' name='username'><br>
 		</td>
		</tr>
 
		<tr>
 			<td style='text-align:center'>
  				<label>Password :</label>
 			</td>
 			<td>
 				<input type='password' name='password'>
 			</td>
		</tr>
		
		<tr>
			<td>
  			
 			</td>
 			<td>
  			<input type='submit' class="bbutton" value='Login'>
 			</td>
		</tr>
	</table>
</form>
