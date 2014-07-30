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
$query="select * from test_submissions natural join submissions order by submissionid DESC";
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
	<th><strong>ID</strong></th>
	<th><strong>Username</strong></th>
	<th><strong>Problem</strong></th>
	<th><strong>Verdict</strong></th>
	<th><strong>Time Stamp</strong></th>
	<th><strong>Language</strong></th>
	<th><strong>Execution Time</strong></th>
</tr>
<?php
$i=0;
if($res)
	while($r=mysqli_fetch_array($res))
	{
		$i2=$i+1;
		$submissionid=$r['submissionid'];
		$username=$r['username'];
		$problem_code=$r['problem_code'];
		$verdict=$r['verdict'];
		$submissiontime=$r['submissiontime'];
		$language=$r['language'];
		$executiontime=$r['executiontime'];
		echo "
		<tr";
		if($i%2==1)
			echo " class='alt'";
		echo ">
		<td><a href='./viewsolution.php?submissionid=$submissionid'>$submissionid</a></td>
		<td><a href='./user.php?user=$username'>$username</a></td>
		<td><a href='./viewproblem.php?pcode=$problem_code'>$problem_code</a></td>
		<td><img src='./assets/$verdict.png'></td>
		<td>$submissiontime</td>
		<td>$language</td>
		<td>$executiontime</td>
		
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
