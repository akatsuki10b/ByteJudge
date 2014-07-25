<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
include('./includes/mysql_login_view.php');
error_reporting(-1);

$status=true;
$errorstr='';
if(!isset($_GET['edit']))
{
	$status=false;
	$errorstr="No problem code provided";
	goto end;
}
require('./includes/mysql_login_faculty.php');

$problem_code=$_GET['edit'];
$problem_code=strip_tags(mysqli_real_escape_string($db,$problem_code));
$query="select * from problems where problem_code='$problem_code'";
$res=mysqli_query($db,$query);
if(!$res)
{
	$status=false;
	$errorstr="Error in query";
	goto end;
}
if(mysqli_num_rows($res)!=1)
{
	$status=false;
	$errorstr="No problem with code $problem_code exists";
	goto end;
}
$res=mysqli_fetch_array($res);
$problem_title=$res['problem_title'];
$timelimit=$res['timelimit'];
$showmistakes=$res['showmistakes'];
$type=$res['type'];
$visiblesolutions=$res['visiblesolutions'];

$languagesallowed=array();
$query="select language from problems_languagesallowed where problem_code='$problem_code'";
$res=mysqli_query($db,$query);
if(!($res))
{
	$status=false;
	$errorstr="Error in query";
	goto end;
}
while($r=mysqli_fetch_array($res))
{
	$languagesallowed[]=$r['language'];
	
}

echo "
<h3>Edit Problem</h3>
<form  method='post' enctype='multipart/form-data'  action='./scripts/edit_problem.php'  name='new_problem'>
	<table id='nptable'>
	<tr>
 		<td valign='top'>
			<label><strong>Problem Code :</strong></label>
 		</td>
		<td>
			<input type='hidden' name='problem_code' value='$problem_code'>
  			$problem_code
  	
 		</td>
	</tr>
	<tr>
 		<td valign='top'>
			<label><strong>Problem Title :</strong></label>
 		</td>
		<td>
  			<input  type='text' style='width: 400px;' name='problem_title' value='$problem_title' required x-moz-errormessage='Enter Problem Title'>
 		</td>
	</tr>
 
	
	
	<tr>
 		<td valign='top'>
  			<label><strong>Allowed Languages :</strong></label>
 		</td>
 		<td>
 		";

			
			include_once('./includes/listoflanguages.php');
			
			foreach($languages_names as $code=>$name)
			{
				
 				echo "<input type='checkbox' value='$code' name='languagesallowed[]' ";
 				if(in_array($code,$languagesallowed)!=false)
 					echo " checked=true ";
 				echo  ">$name<br>";
 			}
		echo "
 		</td>
	</tr>
	
	<tr>
 		<td valign='top'>
  			<label><strong>Time Limit :</strong></label>
 		</td>
 		<td>
 			<input type='text' style='width: 100px;' name='time_limit' value='$timelimit' required x-moz-errormessage='Enter integer value'>
 			
            <span> sec </span>
 		</td>
	</tr>
	
	
               
        <!-- <tr>
 		<td valign='top'>
  			<label><strong>Memory Limit :</label>
 		</td>
 		<td>
 			<input type='text' style='width: 100px;' name='memory_limit' required x-moz-errormessage='Enter integer value'>
                        <span> KB </span>
 		</td>
	</tr>
	
	<tr>
 		<td valign='top'>
  			<label><strong>Source Limit :</label>
 		</td>
 		<td>
 			<input type='text' style='width: 100px;' name='source_limit' required x-moz-errormessage='Enter integer value'>
                        <span> KB </span>
 		</td>
	</tr> -->
	<tr> 
 		<td valign='top'>
  			<label><strong>Problem type :</label>
 		</td>
 		<td>
 			<select name='problem_type'>
				<option value='practice' "; if($type=='practice') echo 'selected=true'; echo " >PRACTICE</option>
				<option value='test' "; if($type=='test') echo 'selected=true'; echo " >TEST</option>
			</select>
 		</td>
	</tr>
	
	<tr>
 		<td valign='top'>
  			<label><strong>Show In-depth View to students :</strong></label>
 		</td>
 		<td>
 			<select name='problem_showmistakes'>
				<option value='true' "; if($showmistakes=='true') echo 'selected=true'; echo " >Yes</option>
				<option value='false' "; if($showmistakes=='false') echo 'selected=true'; echo " >No</option>
			</select>
 		</td>
	</tr>
	<tr>
 		<td valign='top'>
  			<label><strong>Make solutions visible to all other students :</strong></label>
 		</td>
 		<td>
 			<select name='problem_visiblesolutions'>
				<option value='true' "; if($visiblesolutions=='true') echo 'selected=true'; echo ">Yes</option>
				<option value='false' "; if($visiblesolutions=='false') echo 'selected=true'; echo ">No</option>
			</select>
 		</td>
	</tr>
	
	<tr>
		<td>
  			
 		</td>
 		<td>
  			<input type='submit' value='Save Problem' class='bbutton'>
 		</td>
	</tr>
	
</table>
</form>
";
end:
if(!$status)
{
	echo "<br><br>";
	echo $errorstr;
}
