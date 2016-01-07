
<?php

$errorstr='';
$status=true;
if(!(isset($_GET['testid'])))
{
	$errorstr="No testid provided";
	$status=false;
	goto end;
}

include('./includes/mysql_login_view.php');
$testid='';
$problems=array();
$studentgroupsvisibleto=array();

$testid=strip_tags(mysqli_real_escape_string($db,$_GET['testid']));
$testid=strtoupper($testid);
$query="select testname,testdetails,visiblefrom,visibletill,createdby,createdon,(UNIX_TIMESTAMP(visiblefrom)-UNIX_TIMESTAMP(CURRENT_TIMESTAMP())) as secondsleft_start,(UNIX_TIMESTAMP(visibletill)-UNIX_TIMESTAMP(CURRENT_TIMESTAMP())) as secondsleft_end from test_main where testid='$testid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$status=false;
	$errorstr="Error in accessing Database";
	goto end;
}
if(mysqli_num_rows($res)!=1)
{
	$status=false;
	$errorstr="No test with ID $testid";
	goto end;
}
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$res=mysqli_fetch_array($res);
$testname=$res['testname'];
$testdetails=$res['testdetails'];
$visiblefrom=$res['visiblefrom'];
$visibletill=$res['visibletill'];
$createdby=$res['createdby'];
$createdon=$res['createdon'];
$secondsleft_end=$res['secondsleft_end'];
$secondsleft_start=$res['secondsleft_start'];
$time_left='';
$time_msg='';
if($secondsleft_start>0)
{
	$time_left=$secondsleft_start;
		$time_msg='Starts in';
}
else if($secondsleft_end>0)
{
	$time_left=$secondsleft_end;
	$time_msg='Ends in';
}
else
{
	$time_msg='Test Ended';
}

		
		
$query="select groupid from test_visibleto where testid='$testid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Cannot fetch participant student groups";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$studentgroupsvisibleto[]=$r['groupid'];
}

$query="select problem_code from test_problems where testid='$testid'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$errorstr="Couldn't fetch problems authored";
	$status=false;
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$problems[]=$r['problem_code'];
}
?>

<a href='#'><h3 style='text-align:center;'><?php echo $testname; ?></h3></a>


<div align='center'>




<?php
if($time_left!='')
{
		echo "
			<div style='color: #46a639; text-align:center; font-weight: bold;' id='countdown'> </div>
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
		    }
		    else
		    	echo $time_msg;
?>
</div>
<br />
<div align='center'>
<strong>Test Details</strong>
</div>
<br />
<div align='center' style='font-size:12px;'>
<?php echo $testdetails; ?></div>
<br />
<table id='nptable'>
<tr>
	<td><strong>Created By: </strong></td>
	<td><?php echo "<a href='./faculty_profile.php?facultyid=$createdby'>$createdby ";?></a></td>
	
</tr>
<tr>
	<td><strong>Created On: </strong></td>
	<td><?php echo $createdon; ?></td>
	
	
</tr>
	
<tr style='background-color:#f7f7f7;'>
	
	<td><strong>Visible From: </strong></td>
	<td><?php echo $visiblefrom; ?></td>
	<td><strong>Visible Till: </strong></td>
	<td><?php echo $visibletill; ?></td>
</tr>

<tr>
	<td><strong>Participant Groups: </strong></td>
	<td>
		<?php
			foreach($studentgroupsvisibleto as $groupid)
			{
				echo "<a href='./studentgroup_profile.php?groupid=$groupid'>$groupid</a>  ";
			}
		?>
	</td>
</tr>
<tr>
	<td><strong>Problems: </strong></td>
	<td>
		<?php
			foreach($problems as $probcode)
			{
				echo "<a href='./viewproblem.php?pcode=$probcode'>$probcode</a>  ";
			}
		?>
	</td>
</tr>
<tr>
<td>
<a href='./test_attemptedby.php?testid=<?php echo $testid; ?>'>

<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Attempted By'/>
</a>
				
</td>

<td>
<form action='./scripts/test_resetstats.php' method='post'>

<input type='hidden' name='testid' <?php echo "value='$testid'"; ?> >
<input type='submit' id='buttonblue' name='buttonblue' class='rbutton' value='Reset Stats'  onclick="return confirm('Are you sure?');" />
</form>			
</td>
<td>
<a href='./test.php?testid=<?php echo $testid; ?>'>

<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='View test'/>
</a>
				
</td>
<td>
<a href='./leaderboard.php?testid=<?php echo $testid; ?>'>

<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Leaderboard'/>
</a>
				
</td>
</tr>
<tr>
<td>
<a href='./test_submissions.php?testid=<?php echo $testid; ?>'>

<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Submissions'/>
</a>
				
</td>
<td>
<form action='./scripts/deletetests.php' method='post'>
<input type='hidden' name='teststodelete[]' <?php echo "value='$testid'"; ?> >
<input type='submit' id='buttonblue' name='buttonblue' class='rbutton' value='Delete Test' onclick="return confirm('Are you sure?');"/>
</form>
</td>
<td>
<a href='./new_test.php?edit=<?php echo $testid; ?>'>

<input type='submit' id='buttonblue' name='buttonblue' class='bbutton' value='Edit test'/>
</a>
				
</td>


<tr>
</table>


<?php
end:
if(!$status)
{
	echo $errorstr;
}
?>
