<?php
require('./determineidentityfunc.php');
if(!(isloggedin("faculty")==true))//change so that admin can too add problem
{
	header('Location: ./../home.php');
}
else
{

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	include('./../includes/mysql_login_faculty.php');//change this later
	error_reporting(-1);
	mysqli_autocommit($db,false);
	$problem_code='';
	$problem_title='';
	$problem_statement='';
	$input_desc='';
	$output_desc='';
	$constraints='';
	$sampleip='';
	$sampleop='';
	$sampleexp='';
	$time_limit='';
	$memory_limit=1000000;
	$type='';
	
	$problem_showmistakes='false';
	$problem_visiblesolutions='false';
	$problem_type='';
	$languagesallowed=array();
	$ipfilesarray=array();
	$opfilesarray=array();
	
	$status=true;
	$errorstr='';
	if(isset($_POST['time_limit']))
		$time_limit=strip_tags(mysqli_real_escape_string($db,$_POST['time_limit']));
	if($time_limit=='')
		$time_limit=0;
	if(isset($_POST['memory_limit']))
		$memory_limit=strip_tags(mysqli_real_escape_string($db,$_POST['memory_limit']));
	if($memory_limit=='')
		$memory_limit=1000000;
	if(isset($_POST['problem_code']))
		$problem_code=strip_tags(mysqli_real_escape_string($db,$_POST['problem_code']));
	if(isset($_POST['problem_title']))
		$problem_title=strip_tags(mysqli_real_escape_string($db,$_POST['problem_title']));
	if(isset($_POST['problem_statement']))
		$problem_statement=$_POST['problem_statement'];
	
	if(isset($_POST['problem_showmistakes']))
		$problem_showmistakes=strip_tags(mysqli_real_escape_string($db,$_POST['problem_showmistakes']));
	if(isset($_POST['problem_visiblesolutions']))
		$problem_visiblesolutions=strip_tags(mysqli_real_escape_string($db,$_POST['problem_visiblesolutions']));
	if(isset($_POST['problem_type']))
		$problem_type=strip_tags(mysqli_real_escape_string($db,$_POST['problem_type']));
	if(isset($_POST['input_desc']))
		$input_desc=$_POST['input_desc'];
	if(isset($_POST['output_desc']))
		$output_desc=$_POST['output_desc'];
	if(isset($_POST['constraints']))
		$constraints=$_POST['constraints'];
	if(isset($_POST['sampleip']))
		$sampleip=$_POST['sampleip'];
	if(isset($_POST['sampleop']))
		$sampleop=$_POST['sampleop'];
	if(isset($_POST['sampleexp']))
		$sampleexp=$_POST['sampleexp'];
	if(isset($_POST['languagesallowed']))
		$languagesallowed=$_POST['languagesallowed'];
	//$languagesallowed=implode(',',$languagesallowed);
	if(isset($_FILES['ipfiles']))
		$ipfilesarray=$_FILES['ipfiles'];
	if(isset($_FILES['opfiles']))
		$opfilesarray=$_FILES['opfiles'];

	//$problem_code="see";
	$problem_code=strtoupper($problem_code);
	echo "problem code is $problem_code";
	$currentuser=$_SESSION['username'];

	$query="select problem_code from problems where problem_code='$problem_code'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		$errorstr="error in prequery";
		$status=false;
	//	$errorstr.=mysqli_error($db);
		goto end;
	}
	$res=mysqli_affected_rows($db);
	if($res!=0)
	{
		$errorstr="Problem by this code already exists";
		$status=false;
		goto end;
	}
	chdir('./..');
	chdir('PROBLEMS');
	$exists=shell_exec("ls $problem_code");
	if($exists)
	{
	
		$remove=shell_exec("rm -r $problem_code;echo $?");
		if($remove!=0)
		{
			$errorstr= "Folder by the name $problem_code already exists. Cannot delete it.";
			$status=false;
			goto end;
		}
	}
	$makedir=shell_exec("mkdir $problem_code 2>&1");
	if($makedir)
	{
		$errorstr= "Cannot make directory$makedir";
		$status=false;
		goto end;
	}
	chdir($problem_code);
	$makedir=shell_exec("mkdir IPOPFILES;echo $?");
	if($makedir!=0)
	{
		$errorstr="Cannot make directory";
		$status=false;
		goto end;
	}
	echo shell_exec("pwd");
	echo (file_put_contents("test","op"));
	$res=true;
	$res&=(file_put_contents("title",$problem_title));
	$res&=(file_put_contents("statement",$problem_statement));
	$res&=(file_put_contents("input_desc",$input_desc));
	$res&=(file_put_contents("output_desc",$output_desc));
	$res&=(file_put_contents("sampleip",$sampleip));
	$res&=(file_put_contents("sampleop",$sampleop));
	$res&=(file_put_contents("sampleexp",$sampleexp));
	$res&=(file_put_contents("constraints",$constraints));
	if($res==false)
	{
		//echo "$res Couln't make one or more files";
	//$res is false even if any one of the files is empty. i.e. any one of the test boxes is left empty
	}
	chdir("IPOPFILES");
	$res=true;
	$numbertestfiles=0;
	for($i=0;true;$i++)
	{
		$fileno=$i+1;
		if(isset($opfilesarray['tmp_name'][$i]) && isset($ipfilesarray['tmp_name'][$i]) && $opfilesarray['size'][$i]!=0 && $ipfilesarray['size'][$i]!=0)
		{
			$numbertestfiles=$fileno;
		
			$res&=(move_uploaded_file($ipfilesarray['tmp_name'][$i],"ip$fileno"));
			$res&=(move_uploaded_file($opfilesarray['tmp_name'][$i],"op$fileno"));
		}
		else
			break;
	}
	if($res==false)
	{
		echo "cannot move one or more input files";
	}

	$query="insert into problems(problem_code,addedon,addedby,number_testfiles,timelimit,memorylimit,type,showmistakes,problem_title,visiblesolutions) values('$problem_code',CURRENT_TIMESTAMP(),'$currentuser',$numbertestfiles,$time_limit,$memory_limit,'$problem_type','$problem_showmistakes','$problem_title','$problem_visiblesolutions')";
	// echo $query;
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		echo mysqli_error($db);
		$errorstr= "Couldn't insert successfully in database";
		$status=false;
		goto end;
	}
	$query="insert into problems_languagesallowed(problem_code,language) values('$problem_code',";
	foreach($languagesallowed as $l)
	{
		$l=strip_tags(mysqli_real_escape_string($db,$l));
		$res=mysqli_query($db,$query."'$l')");
		if($res!=true)
		{
			$status=false;
			$errorstr="couldn't insert in languages allowed";
			goto end;
		}
	}
	end:
	if($status)
	{
		mysqli_commit($db);
		mysqli_close($db);
		header("Location: ./../new_problem.php?prev=done");
	}
	else
	{
//		echo mysqli_error($db);
		mysqli_rollback($db);
		mysqli_close($db);
	//	echo "$errorstr";
		header("Location: ./../new_problem.php?prev=fail&msg=$errorstr");
	}
	;
}
?>
