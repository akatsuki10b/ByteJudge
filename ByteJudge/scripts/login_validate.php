<?php
if(isset($_POST['username']) && isset($_POST['password']))
{
	//change this.. mysql_login_admin thing.
	include('./../includes/mysql_login_login.php');
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	$username=strip_tags(mysqli_real_escape_string($db,$_POST['username']));
	$password=strip_tags(mysqli_real_escape_string($db,$_POST['password']));
	$username=strtoupper($username);
	$status=true;
	$errorstr='';
	$query="select type from users where userid='$username'";
	$res=mysqli_query($db,$query);
	if(mysqli_num_rows($res)!=1)
	{	
		$status=false;
		//$errorstr="user $username not found";
		$error="invalid username or password";
		goto end;
	}
	
	$tablename='';
	$type='unknown';
	$res=mysqli_fetch_array($res);
	if($res['type']=='student')
	{
		$type='student';
		$query2="select rollno from students_main where rollno='$username'";
		$res2=mysqli_query($db,$query2);
		$res2=mysqli_num_rows($res2);
		if($res2!=1)
		{
			$status=false;
			$errorstr="intrusion detected!";
			goto end;
		}
	}
	else if($res['type']=='faculty')
	{
		$type='faculty';
		$query2="select facultyid from faculty_main where facultyid='$username'";
		$res2=mysqli_query($db,$query2);
		$res2=mysqli_num_rows($res2);
		if($res2!=1)
		{
			$status=false;
			$errorstr="intrusion detected!";
			goto end;
		}
	}
	else if($res['type']=='admin')
	{
		$type='admin';
		$query2="select adminid from admin_main where adminid='$username'";
		$res2=mysqli_query($db,$query2);
		$res2=mysqli_num_rows($res2);
		if($res2!=1)
		{
			$status=false;
			$errorstr="intrusion detected!";
			goto end;
		}
		
	}
	
	$query="select * from logininfo where username='$username' && password=SHA1('$password')";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$errorstr=mysqli_error($db);
		$status=false;
		goto end;
	}
	if(mysqli_num_rows($res)!=1)
	{

		$errorstr="Invalid username or password";
		$status=false;
		goto end;
	}

	$query="update logininfo set lastlogin=CURRENT_TIMESTAMP() where username='$username'";
	$res=mysqli_query($db,$query);
	
	session_start();
	$_SESSION['username']=$username;
	$_SESSION['type']=$type;
	
	end:	
	mysqli_close($db);
	if($status)
	{
		header("Location: ./../home.php?loggedin=true");		
	}
	else
	{
//		echo $errorstr;
		header("Location: ./../login.php?loggedin=false&error=$errorstr");
	}
}
else
{
	header("Location: ./../home.php");
}
?>
