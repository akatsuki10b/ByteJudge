<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
include('./includes/mysql_login_view.php');
error_reporting(-1);
$query="select testid from test_main";
$res=mysqli_query($db,$query);
$testid_array=array();
while($r=mysqli_fetch_array($res))
{
	$testid_array[]=$r['testid'];
}

$edit=false;
$edit_tid='';

if(isset($_GET['edit']))
{
	$edit_tid=mysqli_real_escape_string($db,$_GET['edit']);
	$edit_tid=strtoupper($edit_tid);
	foreach($testid_array as $testid)
	{
		if($testid==$edit_tid)
		{
			$edit=true;
			break;
		}
	}
	if($edit)
	{
		
		$query="select * from test_main where testid='$edit_tid'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$edit=false;
		}
		else
		{

			$res=mysqli_fetch_array($res);
			$testid=$res['testid'];
			$testname=$res['testname'];
			$testdetails=$res['testdetails'];
			$visiblefrom=$res['visiblefrom'];
			$visibletill=$res['visibletill'];
			$from_year=substr($visiblefrom,0,4);
			$from_month=substr($visiblefrom,5,2);
			$from_day=substr($visiblefrom,8,2);
			$from_hour=substr($visiblefrom,11,2);
			$from_minute=substr($visiblefrom,14,2);
			$from_second=substr($visiblefrom,17,2);

			$till_year=substr($visibletill,0,4);
			$till_month=substr($visibletill,5,2);
			$till_day=substr($visibletill,8,2);
			$till_hour=substr($visibletill,11,2);
			$till_minute=substr($visibletill,14,2);
			$till_second=substr($visibletill,17,2);
			
			
			$problemsintest=array();
			$studentgroupsintest=array();
		
			$query="select problem_code from test_problems where testid='$testid'";
			$res=mysqli_query($db,$query);
			if(!($res))
			{
				$edit=false;	
				echo mysqli_error($db);
				goto after;
			}
			while($r=mysqli_fetch_array($res))
			{
				$problemsintest[]=$r['problem_code'];
			}
		
			$query="select groupid from test_visibleto where testid='$testid'";
			$res=mysqli_query($db,$query);
			if(!($res))
			{
				$edit=false;
				goto after;
			}
			while($r=mysqli_fetch_array($res))
			{
				$studentgroupsintest[]=$r['groupid'];
			}		
		}
	}

}

after:
;

if(isset($_GET['prev']))
{
	echo "<div style='text-align: center;' >";
	if($_GET['prev']=="done")
	{
		echo "
		
			<img src='./assets/valid.png'>
			<h4 style='color: #55bc48; display:inline-block;'> Test Creation Successful </h4> 
		
		";
	}
	else if($_GET['prev']=="partial")
	{
		echo "
		
			<img src='./assets/valid.png'>
			<h4 style='color: #55bc48; display:inline-block;'> Test Creation Partially Successful </h4> 
		";
		if(isset($_GET['partialfailmsg']))
		{
			$partialfailmsg=$_GET['partialfailmsg'];
			echo "<p style='color: #8F8F8F;'> $partialfailmsg </p>";
		}
	}
	else if($_GET['prev']=="fail")
	{
		echo "
	
			<img src='./assets/invalid.png'>
			<h4 style='color: #e74833; display:inline-block;'> Test Creation Unsuccessful </h4> 
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
<h3>Create New Test</h3>
<form  method='post'  action='./scripts/make_newtest.php'  name='new_test'>
<table id='nptable'>
	<tr>
	 	<td>
			<label><strong>Test ID :</strong></label>
	 	</td>
		<td>
			<?php
				if($edit)
				{
					echo $testid;//make better for eyes	
					echo "<input type='hidden' id='testid' name='testid' value='$testid' maxlength='20'>";
				}
				else
				{
  					echo  "<input  type='text' style='width: 200px;' name='testid' required onblur='validate_testid(this)'>";
				}
			?>
	  		
	  		<script language='javascript' type='text/javascript'>
			function validate_testid(input)
			{
				input.value = input.value.toUpperCase();
				var testid_list = new Array();
				<?php
					$i=0;
					foreach($testid_array as $tid)
					{
						echo "testid_list[$i]='$tid';
						";
						$i++;
					}
				?>
				
				for(i=0;i<testid_list.length;i++)
				{
					if(testid_list[i] == input.value)
					{
						input.setCustomValidity('Sorry, this Test ID is already taken.');
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
 		<td valign='top'>
			<label><strong>Test Name :</strong></label>
 		</td>
		<td>
  			<input  type='text' style='width: 300px;' name='testname' <?php if($edit){ echo "value='$testname'";} ?> required x-moz-errormessage='Enter Test Name'>
 		</td>
	</tr>
 
	<tr>
 		<td valign='top'>
  			<label><strong>Test Details :</strong></label>
 		</td>
 		<td>
 			<textarea  name='testdetails' cols='50' rows='5'><?php if($edit){ echo $testdetails;} ?></textarea>
 		</td>
	</tr>
	
	
	<tr>
 		<td>
  			<label><strong>Visible From :</strong></label>
 		</td>
 		<td>
 			<?php
				include("./includes/datetime.php"); 
				if($edit) 
				{
					dateandtime("from_",$from_year,$from_month,$from_day,$from_hour,$from_minute,$from_second);
				}
				else
				{
					dateandtime("from_",-1,-1,-1,-1,-1,-1);
				}
		
				
			?>
 		</td>
	</tr>
	
	<tr>
 		<td>
  			<label><strong>Visible Till :</strong></label>
 		</td>
 		<td>
 			<?php 
				if($edit) 
				{
					dateandtime("till_",$till_year,$till_month,$till_day,$till_hour,$till_minute,$till_second);
				}
				else
				{
					dateandtime("till_",-1,-1,-1,-1,-1,-1);
				}
		
			 ?>
 		</td>
	</tr>
	
	<?php include("./includes/select_problem_form.php"); ?>
	
	<?php include("./includes/select_student_group_form.php"); ?>
	<tr>
		<td>
  			
 		</td>
 		<td>
			<?php
				if($edit)
					echo "<input type='hidden' name='edit' value='$groupid'>";
			?>
  			<input type='submit' value='Create Test' class='bbutton'>
 		</td>
	</tr>
</table>
</form>
