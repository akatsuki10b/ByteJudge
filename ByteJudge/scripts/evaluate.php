<?php
//*********************remove all echos 
//*********************strip_tags(mysqli_real_escape_string($db, whereever required
function linecmp($lineuserop,$lineop)
{
	$i=0;
	echo "comparing $lineuserop and $lineop<br>";
	$len1=strlen($lineop);
	$len2=strlen($lineuserop);
	echo "lengths $len1 and $len2<br>";
	for($i=0;isset($lineop[$i]) && $lineop[$i]!='';$i++)
	{
		if(isset($lineuserop[$i]) && $lineuserop[$i]==$lineop[$i])
			continue;
		return false;
	}
	return true; 
}
function filecmp($userop,$op)
{
	$lineoparr=array();
	$oparrsize=0;
	$checkpoint=0;
	while(($lineop=fgets($op))!=false)
	{
		$lineoparr[]=trim($lineop);
	//	echo $lineop;//REMOVE THIS LATER
		$oparrsize++;
		if(trim($lineop)!="")
		{
			$checkpoint=$oparrsize;
		}
	}
	$oparrsize=$checkpoint;
	$lineuseroparr=array();
	$useroparrsize=0;
	$checkpoint=0;
	while(($lineuserop=fgets($userop))!=false)
	{
		$lineuseroparr[]=trim($lineuserop);
	//	echo $lineuserop;//REMOVE THIS LATER
		$useroparrsize++;
		if(trim($lineuserop)!="")
		{
			$checkpoint=$useroparrsize;
		}
	}
	$useroparrsize=$checkpoint;
	if($oparrsize!=$useroparrsize)
	{
		return false;
	}
	for($i=0;$i<$oparrsize;$i++)
	{
		if($lineuseroparr[$i]!=$lineoparr[$i])
		{	
			
			return false;
		}
	}
	return true;
}
require('./determineidentityfunc.php');

if(!(isloggedin("any")==true) )
{
	header('Location: ./../home.php');
}
else
{
	include('./../includes/mysql_login_test.php');
	require('./isstudentauthorised_test.php');
	if(isset($_POST['testid']) && $_SESSION['type']!="admin" && $_SESSION['type']!="faculty" && isstudentauthorised($db,$_SESSION['username'],$_POST['testid'])!=1)
	{
		mysqli_close($db);
		$testid=$_POST['testid'];
		echo "here";
	//	header("Location: ./../test.php?testid=$testid");
	}
	else
	{
		mysqli_close($db);
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		error_reporting(-1);
	
	
	if(isset($_FILES['solution']) && $_FILES['solution']['size']>0 && isset($_POST['language']) && isset($_SESSION['username']) && isset($_POST['username']) && ($_POST['username']==$_SESSION['username']) )
		{
			
			$errorstr='';
			$status=true;
		
			include_once('./../includes/listoflanguages.php');
			$filename=$_FILES['solution']['name'];
			$filetype=$_FILES['solution']['type'];
			$filesize=$_FILES['solution']['size'];
			$tname=$_FILES['solution']['tmp_name'];
			$extension = substr($filename, strripos($filename,'.'));
			$username=$_POST['username'];
			$probcode=$_POST['probcode'];
			$language=$_POST['language'];
			$compiler=$languages_compilers[$language];
			$extension=$languages_extensions[$language];
			//SEE EXIT CODES FOR JAVA
		
			include('./../includes/mysql_login_evaluate.php');
			mysqli_autocommit($db,false);
			$query="insert into submissions(problem_code,username,submissiontime,verdict,executiontime,language,count_ac,count_wa,count_rte,count_tle) values ('$probcode','$username',CURRENT_TIMESTAMP(),'PENDING',NULL,'$language',0,0,0,0)";
	
			$res=mysqli_query($db,$query);
			if($res)
			{
				echo "initial insertion done";
			}
			else
			{
				$errorstr= "initial insertion failed";//what to do if initial insertion fails;
				$status=false;
				goto end;
		
			}
			$subid= mysqli_insert_id($db);
	//		$timestamp=date("YmdHms");
			$timeofexecution=0.00;
			$newfilename=$subid.$extension;
			chdir("./../");//confirm this********************
			chdir("submissions");
		
			$res=shell_exec("ls $username");
		
		
			if(!($res))
			{
				shell_exec("mkdir $username 2>&1");
				shell_exec("mkdir $username/$probcode 2>&1");
			}
			chdir($username);
			$res=shell_exec("ls $probcode");
			if(!($res))
			{
				shell_exec("mkdir $probcode");
			}

			chdir("$probcode");
		
			$pathandname="./$username/$probcode/$newfilename";
		
			if($r=move_uploaded_file($_FILES['solution']['tmp_name'],$newfilename))
			{
			}
			else
			{
				$errorstr="not uploaded successfully";
				$status=false;
				goto end;
			}
	
		
			$compileres=shell_exec("$compiler -o $subid.out $newfilename 2>&1;echo $?>compileerrorlog");
			$compileerror=fopen("compileerrorlog","r");	
			$line=fgets($compileerror);		
			$endresult=0;
			$count_tle=0;
			$count_wa=0;
			$count_rte=0;
			$count_ac=0;
			$wrongcase_input='';
			$wrongcase_output='';
			$wrongcase_expectedoutput='';
			$wrongcase_verdict=0;
			$showmistakes='false';
			if($line!=0)
			{
				$endresult=-1;			
			}
			else
			{
			
			
				$res=mysqli_query($db,"select timelimit,memorylimit,number_testfiles,showmistakes from problems where problem_code = '$probcode'");	//CHECKING IF THE PROBLEM IS IN THE TABLE OR NOT
				$res=mysqli_fetch_array($res);
				$timelimit=$res['timelimit'];
				$memorylimit=$res['memorylimit'];
				$numberoffiles=$res['number_testfiles'];
				$showmistakes=$res['showmistakes'];
			
				for($i=1;$i<=$numberoffiles;$i+=1)
				{
			echo shell_exec("ulimit -t $timelimit; time ./$subid.out < ../../../PROBLEMS/$probcode/IPOPFILES/ip$i > op$subid 2>timelog;echo $?>errorlog");
	/*		echo "ulimit -t $timelimit; time ./$subid.out < ../../../PROBLEMS/$probcode/IPOPFILES/ip$i > op$subid 2>timelog;echo $?>errorlog";*/
					echo "ran ./$subid.out";
					$timelog=fopen("timelog","r");
					$errorlog=fopen("errorlog","r");
				
					$line=fgets($timelog);
					$linearr=explode(" ",$line);
					echo $line;
					if($linearr[0]=="Command" && $linearr[1]=="terminated" && $linearr[4][0]=="9")// see which other signals
					{
						echo "tle";
						$count_tle++;
						if($wrongcase_verdict<3 && $showmistakes=='true')
						{
							$wrongcase_verdict=3;
							$wrongcase_input=file_get_contents("../../../PROBLEMS/$probcode/IPOPFILES/ip$i");
							$wrongcase_output="TIME LIMIT EXCEEDED";
							$wrongcase_expectedoutput=file_get_contents("../../../PROBLEMS/$probcode/IPOPFILES/op$i");
						}
						//$endresult=3;
						echo shell_exec("rm op$subid");
						echo shell_exec("rm timelog");
						echo shell_exec("rm errorlog");
						continue;
					}
		
					$usertime=$linearr[0][0].$linearr[0][1].$linearr[0][2].$linearr[0][3];
					$systime=$linearr[1][0].$linearr[1][1].$linearr[1][2].$linearr[1][3];
				
					$usertime=floatval($linearr[0]);
					$systime=floatval($linearr[1]);
					echo " $usertime and $systime";
					$timeofexecution+=($usertime+$systime);
					$line=fgets($errorlog);
					if($line>=126)//CONFIRM THIS
					{	
						$count_rte++;
				
					//	$endresult=4;
						if($wrongcase_verdict<4  && $showmistakes=='true')
						{
							$wrongcase_verdict=4;
							$wrongcase_input=file_get_contents("../../../PROBLEMS/$probcode/IPOPFILES/ip$i");
							$wrongcase_output="RUN TIME ERROR. RETURN CODE $line";
							$wrongcase_expectedoutput=file_get_contents("../../../PROBLEMS/$probcode/IPOPFILES/op$i");
						}
						echo shell_exec("rm op$subid");
						echo shell_exec("rm timelog");
						echo shell_exec("rm errorlog");
						continue;
					}
					$useropfile=fopen("op$subid","r");
					$reqop=fopen("../../../PROBLEMS/$probcode/IPOPFILES/op$i","r");
		//			echo "opfile is $reqop";
					if(!($reqop))
						echo "cannot open req file";
					if(!($useropfile))
						echo "cannot open useropfile";
					if(filecmp($useropfile,$reqop))
					{
						$count_ac++;
						echo "right in file $i";
					}
					else 
					{
						$count_wa++;	
						if($wrongcase_verdict<2  && $showmistakes=='true')
						{
							$wrongcase_verdict=2;
							/*$wrongcase_input=file_get_contents("../../../PROBLEMS/$probcode/IPOPFILES/ip$i");
							$wrongcase_output=file_get_contents("op$subid");
							$wrongcase_expectedoutput=file_get_contents("../../../PROBLEMS/$probcode/IPOPFILES/op$i");*/
							$ipfile=fopen("../../../PROBLEMS/$probcode/IPOPFILES/ip$i","r");
							$opfile=fopen("op$subid","r");
							$expectedopfile=fopen("../../../PROBLEMS/$probcode/IPOPFILES/op$i","r");
							
							$wrongcase_input.=fread($ipfile,100);
						
							$wrongcase_output.=fread($opfile,100);
						
							$wrongcase_expectedoutput.=fread($expectedopfile,100);
						//	$wrongcase_expectedoutput.=fread($expectedopfile,100);
						}
						echo "wrong in file $i";
					//	$endresult=2;
					
					}
				
					echo shell_exec("rm op$subid");
					echo shell_exec("rm timelog");
					echo shell_exec("rm errorlog");
				}
			}
			echo shell_exec("rm compileerrorlog");
			echo shell_exec("rm $subid.out");
			$verdict='';
			echo "endresult is $endresult";
			if($endresult==0)
			{
				if($count_rte!=0)
				{
					$endresult=4;
				}
				else if($count_tle!=0)
				{
					$endresult=3;
				}
				else if($count_wa!=0)
				{
					$endresult=2;
				}
			}
		
			if($endresult==-1)
			{
				$verdict="CTE";
				echo "Compile Time error<br>$compileres";
			}
			else if($endresult==0)
			{
	
				$verdict="AC";
				echo "Correct Answer Time of execution $timeofexecution";
			}
			else if($endresult==2)
			{
			
				$verdict="WA";
				echo "Wrong Answer  Time of execution $timeofexecution";
			}
			else if($endresult==3)
			{
			
				$verdict="TLE";
				echo "Time Limit Exceeded";
			}
			else if($endresult==4)
			{
		
				$verdict="RTE";
				echo "Run time error Exit code: $line Time of execution $timeofexecution";
			}
			echo "RTE:$count_rte<br>WA:$count_wa<br>TLE:$count_tle<br>AC:$count_ac<br>";		
			$query="update submissions set verdict='$verdict',executiontime=$timeofexecution,count_ac=$count_ac,count_wa=$count_wa,count_rte=$count_rte,count_tle=$count_tle where submissionid=$subid";
			$res=mysqli_query($db,$query);
			if(!($res))
			{
				$errorstr="error in final updation";
				$errorstr=mysqli_error($db);
				$status=false;
				goto end;
			}
			end:
		
			if($status)
			{
				if(isset($_POST['testid']))
				{
					$testid=$_POST['testid'];
					$query="insert into test_submissions(testid,submissionid) values('$testid','$subid')";	
					$res=mysqli_query($db,$query);
					if(!($res))
					{
						$status=false;
						$errorstr="Error in updating test stats";
				//		$errorstr=mysqli_error($db);
						goto end;
					}
					//not checking if successful or not for now... no dire need
				}
				mysqli_commit($db);
				mysqli_close($db);
				
				if($endresult==-1)
				{
					echo "<form id='redirect_cte' method='post' action='./../viewsolution.php?submissionid=$subid&a'>
					<input type='hidden' name='submissionid' value='$subid'>
					<input type='hidden' name='compileerror_details' value='$compileres'>
					
					<input type='submit' value='click here to continue'>
					</form>			
			
					<script type='text/javascript'>
					  document.getElementById('redirect_cte').submit();
					</script>

					";
				}
				else if($showmistakes=='false' || $wrongcase_verdict==0)
				header("Location: ./../viewsolution.php?submissionid=$subid");
			
				else
				{
					echo "<form id='redirect' method='post' action='./../viewsolution.php?submissionid=$subid'>
					<input type='hidden' name='submissionid' value='$subid'>
					<input type='hidden' name='wrongcase_input' value='$wrongcase_input'>
					<input type='hidden' name='wrongcase_output' value='$wrongcase_output'>			
					<input type='hidden' name='wrongcase_expectedoutput' value='$wrongcase_expectedoutput'>
					<input type='submit' value='click here to continue'>
					</form>			
			
					<script type='text/javascript'>
					  document.getElementById('redirect').submit();
					</script>

					";
				}
		
			}
			else
			{
				//echo mysqli_error($db);
			
				mysqli_rollback($db);
				mysqli_close($db);
				header("Location: ./../home.php?msg=$errorstr");
			}
		
		
		
		
		}	
	}
}
?>
