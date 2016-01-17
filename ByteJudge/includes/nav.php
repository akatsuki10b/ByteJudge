
<?php

echo "

<div class='nav-bar'>
	<a href='./home.php'>Home</a>
	<a href='./problems_practice.php'>Practice</a>
	";
	if(isset($_SESSION['type']) && $_SESSION['type']=="faculty")
	{
		echo "<a href='./view_alltests.php'>Tests</a>";
	}
	if(!isset($_SESSION['type']) || $_SESSION['type']=="student")
	{
		echo "<a href='./view_mytests.php'>Tests</a>";
	}
	if(session_id()=="")
		session_start();
	if(isset($_SESSION['username']) && isset($_SESSION['type']))
	{
		$username=$_SESSION['username'];
		/*$type=$_SESSION['type'];
		$link='';
		if($type=="student")
			$link="./student_profile.php?rollno=$username";
		else if($type=="faculty")
			$link="./faculty_profile.php?facultyid=$username";

		echo "
		<a href='$link'>$username</a>
		<a href='./scripts/logout.php'>Logout</a>
		";*/
		echo "
		<a href='./user.php?user=$username'>$username</a>
		<a href='./scripts/logout.php'>Logout</a>
		";
	}
	else
	{
		echo "
		<a href='./login.php'>Sign In</a>
		<a href='./student_registration.php'>Sign Up</a>
		";
	}
	echo '
</div> <!-- end nav -->';
?>
