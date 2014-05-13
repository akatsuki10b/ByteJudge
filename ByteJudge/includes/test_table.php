<?php
include('./includes/mysql_login_test.php');
$testid='';
if(isset($_GET['testid']))
	$testid=$_GET['testid'];
$testid=strtoupper($testid);
$testid=strip_tags(mysqli_real_escape_string($db,$testid));

$status=true;
$errorstr="";


if($testid=='')
{
	$status=false;
	$errorstr="No testid provided";
	goto next;
}
$query="select testname,visiblefrom,visibletill,(UNIX_TIMESTAMP(visiblefrom)-UNIX_TIMESTAMP(CURRENT_TIMESTAMP())) as secondsleft_start,(UNIX_TIMESTAMP(visibletill)-UNIX_TIMESTAMP(CURRENT_TIMESTAMP())) as secondsleft_end from test_main where testid='$testid'";

$res=mysqli_query($db,$query);
if(!($res))
{
	$status=false;
	$errorstr="Error! Please contact the admin";
	//$errorstr=mysqli_error($db);
	goto next;
}
if(mysqli_num_rows($res)!=1)
{
	$status=false;
	$errorstr="No test exists by ID $testid";
	goto next;
}

$res=mysqli_fetch_array($res);

$notyet=false;
$time_left=0;
$time_msg='';
$notanymore=false;
$testname=$res['testname'];
$visiblefrom=$res['visiblefrom'];
$visibletill=$res['visibletill'];
$secondsleft_start=$res['secondsleft_start'];
$secondsleft_end=$res['secondsleft_end'];
//echo "$secondsleft_start $secondsleft_end";
include('./scripts/isstudentauthorised_test.php');
if($_SESSION['type']=="student")
{
	$rollno=$_SESSION['username'];
	$res=isstudentauthorised($db,$rollno,$testid);	
	
	if($res==2)
	{
		$notyet=true;
	}
	else if($res==3)
	{
		$notanymore=true;
	}
	else if($res==1)
	{
		;//allowed
	}
	else
	{
		$status=false;
		$errorstr=$res;
		goto next;
	}
}
next:
if($status==false)
{
	echo $errorstr;
}
else
{

	$attempt=false;//decides if have to print problems or not
	$attemptnow='';//if attempt now button was pressed
	
	if(isset($_POST['attemptnow']))
		$attemptnow=$_POST['attemptnow'];
	
	if($_SESSION['type']=="admin" || $_SESSION['type']=="faculty")
		$attempt=true;
	else if($_SESSION['type']=="student")
	{
		if($notyet==true || $notanymore==true)
		{
			$attempt=false;
		}
		else if($attemptnow!='')
		{
			$query="insert into test_attemptedby(testid,rollno) values('$testid','$rollno')";
			$res=mysqli_query($db,$query);
			if(!($res))
			{
				$status=false;
				$errorstr="Error! Please contact admin";
				
				//goto end;
			}
			else
				$attempt=true;
		}
//		else //this change here
		{
		
			$attempt=false;
			$query="select * from test_attemptedby where testid='$testid' and rollno='$rollno'";
			$res=mysqli_query($db,$query);
			if(!($res))
			{
				$status=false;
				$errostr="Error! Please contact admin";
			
				goto end;
			}
			else
			{
				$status=true;
				$errorstr='';
			}
			if(mysqli_num_rows($res)==1)
			{
				$attempt=true;
				
			}
		}
	}
	if($attempt==false)
	{
	
		
		echo "
		<a href='#'><h3 style='text-align:center;'>$testname</h3></a>
        <table id='nptable'>
		<tr style='background-color:#f7f7f7;'>
	        <td><strong>Visible From: </strong></td>
	        <td>$visiblefrom</td>
	        <td><strong>Visible Till: </strong></td>
	        <td>$visibletill</td>
        </tr>
		</table>
		<br>
		<div align='center'>
		";
		if($notyet==false && $notanymore==false)
		{
				echo "
			<form action='./test.php?testid=$testid' method='post'>
				<input type='submit' id='attempt' name='attemptnow' class='button blue' value='Attempt Test'/>
			</form>
			";
		}
		if($notanymore==true)
		{
			echo "Test ended";
		}
		if($notyet==true)
		{
		    
			echo "<br><div style='color: #46a639; text-align:center; font-weight: bold;' id='countdown'> </div>
			    
		";
			$time_left=$secondsleft_start;
			$time_msg='Starting in ';
		}
		echo "
		<br />
		</div>
		";
	}
	else
	{
		
		$query="select problem_code,problem_title,totalsubmissions,acceptedsubmissions,solvedby from (select * from test_problems where testid='$testid') as A natural join (select problem_code,problem_title from problems) as B";
		//echo $query;
		$res=mysqli_query($db,$query);
		
		if(!($res))
		{
			$status=false;
			$errorstr="Error in fetching problems";
			goto end;
		}
		
		$time_left=$secondsleft_end;
		$time_msg='Ending in';
		echo "<br><div style='color: #46a639; text-align:center; font-weight: bold;' id='countdown'> </div>
			    
		";
		echo "
		<h3 style='text-align:center;'>$testname</h3>
		<table id='table'>
		<tr>
			<th><strong>Problem</strong></th>
			<th><strong>Code</strong></th>
			<th><strong>Accepted Submissions</strong></th>
			<th><strong>Total Submissions</strong></th>
			<th><strong>Solved By</strong></th>
			<th><strong>Attempt</strong></th>
		</tr>";
		$i=0;
		while($r=mysqli_fetch_array($res))
		{
			$problem_code=$r['problem_code'];
			$problem_title=$r['problem_title'];
			$totalsubmissions=$r['totalsubmissions'];
			$acceptedsubmissions=$r['acceptedsubmissions'];			
			$solvedby=$r['solvedby'];
			echo "
			<tr";
			if($i%2==1)
				echo " class='alt'";
			echo ">
			
		 	<td>$problem_title</td>
			<td>$problem_code</td>
			<td>$acceptedsubmissions</td>
			<td>$totalsubmissions</td>
			<td>$solvedby</td>
			<td align='center'>
			<form method='post' action='./viewproblem.php'>
			<input type='hidden' name='testid' value='$testid'>
			<input type='hidden' name='problem_code' value='$problem_code'>
			<input type='submit' id='attempt' name='attempt' class='gbutton' value='Attempt'/>
			</form>
			</td>
		
			</tr>
			";
			$i++;
			//accuracy solved etc.
		}
		echo "
		</table>
		<br>
		<a href='./leaderboard.php?testid=$testid'><input type='button' value='Leaderboard' class='bbutton' ></a>
		";
	}
	echo "
		  <script type='text/javascript'>
		    // set the date we're counting down to
		   
		     
		    // variables for time units
		    var days, hours, minutes, seconds;
		    var seconds_left = 0;
		    // get tag element
		    var countdown = document.getElementById('countdown');
		     var seconds_left_main=$time_left
		    // update the tag with id 'countdown' every 1 second
		    setInterval(function () 
		    {
		        // find the amount of 'seconds' between now and target
		        var current_date = new Date().getTime();
		        
			   
			    seconds_left_main--;
			    seconds_left=seconds_left_main;
		        // do some time calculations
		        days = parseInt(seconds_left / 86400);
		        seconds_left = seconds_left % 86400;
		         
		        hours = parseInt(seconds_left / 3600);
		        seconds_left = seconds_left % 3600;
		         
		        minutes = parseInt(seconds_left / 60);
		        seconds = parseInt(seconds_left % 60);
		         
		        // format countdown string + set tag value
		        if(days <= 0 && hours <= 0 && minutes <= 0 && seconds <= 0)
		        {
		        	countdown.innerHTML = 'Refresh Now!';
		        }
		        else if(days == 0)
		        {
		        	countdown.innerHTML = '$time_msg ' + hours + 'h, ' + minutes + 'm, ' + seconds + 's';
		        }
		        else
		        {
		        	countdown.innerHTML = '$time_msg ' + days + 'd, ' + hours + 'h, ' + minutes + 'm, ' + seconds + 's';
		        }
		     	
		    }, 1000);
		    </script>
		    ";
	end:
	if(!($status))
	{
		echo $errorstr;
	}
}
?>
