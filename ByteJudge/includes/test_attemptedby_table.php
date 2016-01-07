<?php

$errorstr='';
$status=true;
if(!(isset($_GET['testid'])))
{
	$errorstr="No Testid provided";
	$status=false;
	goto end;
}

include('./includes/mysql_login_view.php');
$testid='';
$testid=strip_tags(mysqli_real_escape_string($db,$_GET['testid']));
$testid=strtoupper($testid);
$query="select testname from test_main where testid='$testid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$status=false;
	$errorstr="Error in accessing Database";
	goto end;
}
$res=mysqli_fetch_array($res);
$testname=$res['testname'];
$query="select * from test_attemptedby natural join students_main";
$res=mysqli_query($db,$query);
if(!($res))
{
	$status=false;
	$errorstr="Error in accessing Database";
	goto end;
}

?>
<a href='#'><h3 style='text-align:center;'><?php echo $testname; ?></h3></a>

<table id='table'>
<tr>
	<th><strong>Serial #</strong></th>
	<th><strong>Username</strong></th>
	<th><strong>Name</strong></th>
	
</tr>
<?php
$i=0;
if($res)
	while($r=mysqli_fetch_array($res))
	{
		$i2=$i+1;
		$rollno=$r['rollno'];
		$fullname=$r['fullname'];
		echo "
		<tr";
		if($i%2==1)
			echo " class='alt'";
		echo ">
		<td>$i2</td>
	 	<td>
			<a href='./student_profile.php?rollno=$rollno'>$rollno</a>
	 	</td>
		<td>
	  		<label>$fullname</label>
	 	</td>
		
		</tr>
		";
		$i++;
	}
?>

</table>
<?php
end:
if(!($status))
{
	echo $errorstr;
}
?>
