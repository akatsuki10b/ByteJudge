<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>

<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>

<head>

<meta http-equiv='content-type' content='text/html; charset=utf-8' />

<meta name='description' content='' />

<meta name='keywords' content='' />

<meta name='author' content='' />

<link rel='stylesheet' type='text/css' href='css/style.css' media='screen' />

<title>ByteJudge</title>

</head>

<body>
<div id='wrapper'>
<?php 
require('./scripts/determineidentityfunc.php');
if(!(isloggedin("any")==true))
{
	header('Location: ./login.php');
}
else
{
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	include('./includes/header.php');
	 include('includes/nav.php'); 
	$current_user='';
	$current_usertype='';
	$res='';
	$visiblesolutions='';

	$current_user=$_SESSION['username'];
	$current_usertype=$_SESSION['type'];
	$subid='';
	if(isset($_GET['submissionid']))
		$subid=$_GET['submissionid'];
	if($subid!='')
	{
		include('./includes/mysql_login_user.php');
		$query="select * from submissions where submissionid=$subid";
		$res=mysqli_query($db,$query);
		if(mysqli_num_rows($res)==0)
		{
			$subid='';
		}
		else
		{
			
			$res=mysqli_fetch_array($res);
			$sub_problemcode=$res['problem_code'];
			$query2="select visiblesolutions from problems where problem_code='$sub_problemcode'";
			$res2=mysqli_query($db,$query2);
			$res2=mysqli_fetch_array($res2);
			$visiblesolutions=$res2['visiblesolutions'];
		}
		mysqli_close($db);
	}
	
	if($subid=='' || ($current_usertype=='student' && $res['username']!=$current_user && $visiblesolutions=='false'))//REVISE FACULTY ACCESS
	{
		echo "INVALID PAGE";
		goto end;
	}
	$sub_timestamp=$res['submissiontime'];
	$sub_problemcode=$res['problem_code'];
	$sub_username=$res['username'];
	$sub_verdict=$res['verdict'];
	$sub_countwa=$res['count_wa'];
	$sub_countac=$res['count_ac'];
	$sub_counttle=$res['count_tle'];
	$sub_countrte=$res['count_rte'];
	$sub_executiontime=$res['executiontime'];
	$sub_language=$res['language'];
	//echo "$sub_timestamp";
	echo "
	<br />
	<div>
	<table id='minitable'>
	<tr>
		<th><strong>ID</strong></th>
		<th><strong>Date/Time</strong></th>
		<th><strong>Problem</strong></th>
		<th><strong>Verdict</strong></th>
		<th><strong>User</strong></th>
		<th><strong>Time</strong></th>
		<th><strong>Language</strong></th>
	
	</tr>
	<tr>
		<td>$subid</td>
		<td>$sub_timestamp</td>
		<td><a href='./viewproblem.php?pcode=$sub_problemcode' style='text-decoration:none'>$sub_problemcode</a>
		<td><img src='./assets/$sub_verdict.png'></td>
		<td><a href='./user.php?user=$sub_username' style='text-decoration:none'>$sub_username</a></td>
		<td>$sub_executiontime</td>
		<td>$sub_language</td>
	</tr>
	</table>
	</div> <!-- end #minitable-->
	<br />
	";
	include('./includes/listoflanguages.php');
	$filename="./submissions/$sub_username/$sub_problemcode/$subid";
	$filename.=$languages_extensions[$sub_language];
	
	
	$sol=file_get_contents($filename);
	$lines=substr_count($sol,"
");
	
	
	echo "
<div id='in_depth_view'>
<table id='in_depth_table'>
<tr>
<td style='color: #aaaaaa;text-align:center; background-color:#f5f5f5;'><pre>
";
	for($i=1;$i<=$lines;$i++)
		echo "$i
";
echo
"</pre></td>
<td style='text-align:left; padding:10px;background-color:#fbfbfb;'>
<xmp>";
/*while($line=fgets($solution))
echo "$line
";*/
echo $sol;
	echo "</xmp>
</td></tr></table></div> <!-- end #in_depth_view -->
	<br>
	<div class='tc_analysis'>

	<table id='tc_analysis_table' class='alt'>
	<tr>
	<th>
	<label> <strong> Test Case Analysis </strong> </label>
	</th>
	</tr>
	</table>
	<table id='tc_analysis_table'>
	<tr>
	<td><img src='./assets/AC.png'></td>
	<td><img src='./assets/WA.png'></td>
	<td><img src='./assets/TLE.png'></td>
	<td><img src='./assets/RTE.png'></td>
	</tr>
	<tr>
	<td>$sub_countac</td>
	<td>$sub_countwa</td>
	<td>$sub_counttle</td>
	<td>$sub_countrte</td>
	</tr>
	</table>
	";
	
	if(isset($_POST['wrongcase_input']) && isset($_POST['wrongcase_output']) && isset($_POST['wrongcase_expectedoutput']))
	{
		$wrongfile_input=$_POST['wrongcase_input'];
		$wrongfile_output=$_POST['wrongcase_output'];
		$wrongfile_expectedoutput=$_POST['wrongcase_expectedoutput'];
		echo "
			<div class = 'tc_analysis_title'>
			<label> Input: </label>
			</div>
			<div class = 'tc_analysis_blk'>
			<pre>$wrongfile_input</pre>
			</div>
			<div class = 'tc_analysis_title'>
			<label> Your Output: </label>
			</div>
			<div class = 'tc_analysis_red'>
			<pre>$wrongfile_output</pre>
			</div>
			<div class = 'tc_analysis_title'>
			<label>Expected Output: </label>
			</div>
			<div class = 'tc_analysis_green'>
			<pre>$wrongfile_expectedoutput</pre>
			</div>
			</div>
		";
	}
	if(isset($_POST['compileerror_details']))
	{
		$compileerror_details=$_POST['compileerror_details'];
		echo "
			<div class = 'tc_analysis_title'>
			<label> Compile Error Details </label>
			</div>
			<div class = 'tc_analysis_blk'>
			<pre>$compileerror_details</pre>
			</div>
			
			</div>
		";
	}
	end:
	include('./includes/footer.php'); 
}

?>
</div> <!-- End #wrapper -->

</body>

</html>
