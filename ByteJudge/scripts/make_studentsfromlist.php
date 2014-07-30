<?php
require('./determineidentityfunc.php');
if(!(isloggedin("admin")==true))
{
	
	header('Location: ./../home.php');
}
else
{
	$finalstatus='';

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	include('./../includes/mysql_login_admin.php');
	error_reporting(-1);
	mysqli_autocommit($db,false);
	if(isset($_FILES['student_file']) && $_FILES['student_file']['size']>0)
	{
		
		
		$filename=$_FILES['student_file']['name'];
		if(move_uploaded_file($_FILES['student_file']['tmp_name'],$filename))
		{
			
			
		}
		else
		{
			$finalstatus="Couldn't upload file";
			goto allend;
		}
		
	}
	else
	{
		$finalstatus="No file uploaded";
		goto allend;
	}
	
	$handle = fopen($filename, "r");
	if($handle) 
	{
	 
	    while(!feof($handle))
	    {
		$line=fgets($handle);
		if($line=="")
			continue;
		$rollno='';
		$fullname='';
		$dob='';
		$emailid='';
		$college='';
		$branch='';
		$errorstr='';
		$password='';
	
		$status=true;
		$line=explode(',',$line);
		$linearray=array();
		$i=0;
		$inInverted=false;
		$len_linearray=0;
		foreach($line as $key=>$value)
		{
			
			if($inInverted==true)
			{
				if($value!="" && $value{strlen($value)-1}=='"')
				{
					$value=substr($value,0,strlen($value)-1);
					$inInverted=false;
				}
				$linearray[$i-1].=",$value";
		
			}
			else
			{
				if($value!="" && $value{0}=='"')
				{
					$value=substr($value,1);
					$inInverted=true;
				}	
				$linearray[$i]=$value;
				$i++;
			}
			
		}
		$len_linearray=$i;
	
		if(isset($linearray[0]))
			$rollno=strip_tags(mysqli_real_escape_string($db,$linearray[0]));
		if(isset($linearray[1]))
			$fullname=strip_tags(mysqli_real_escape_string($db,$linearray[1]));
		if(isset($linearray[2]))
			$dob=strip_tags(mysqli_real_escape_string($db,$linearray[2]));
		if(isset($linearray[3]))
			$emailid=strip_tags(mysqli_real_escape_string($db,$linearray[3]));
		if(isset($linearray[4]))
			$college=strip_tags(mysqli_real_escape_string($db,$linearray[4]));
		if(isset($linearray[5]))
			$branch=strip_tags(mysqli_real_escape_string($db,$linearray[5]));
		
		$rollno=strtoupper($rollno);
		
		$password=$rollno;
		if($rollno=='' || $fullname=='' || $password=='')
		{
			$errorstr="not enough details to create new student";
			$status=false;
			goto end;
		}
	
		
		$query="select userid from users where userid='$rollno'";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$errorstr= "error in prequery";
			$status=false;
			goto end;
		}
		$res=mysqli_num_rows($res);	
		if($res>0)
		{	
			$errorstr= "user by userid $rollno already exists";
			$status=false;
			goto end;
		}
		//convert dob to correct format
	
		$query="insert into users(userid,type) values('$rollno','student')";
		
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Couldn't insert data in main table";
			goto end;
		}
		$query="insert into students_main(rollno,fullname,dob,emailid,college,branch,count_submissions,count_AC,count_WA,count_TLE,count_RTE) values('$rollno','$fullname','$dob','$emailid','$college','$branch',0,0,0,0,0)";
		
		$res=mysqli_query($db,$query);
		if(!($res))
		{

			$status=false;
			$errorstr="Couldn't insert data";
			$errorstr=mysqli_error($db);
			goto end;
		}
		$query="insert into logininfo(username,password,createdon) values('$rollno',SHA1('$password'),CURRENT_TIMESTAMP())";
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			//$errorstr=mysqli_error($db);
			$status=false;
			$errorstr="Couldn't create account";
			goto end;
		}
	
		end:
		if($status)
		{	
			$finalstatus.="<br><br>ROLL NO : $rollno : Inserted successfully";
			
			mysqli_commit($db);
			
			
		}
		else
		{
			$finalstatus.="<br><br>ROLL NO : $rollno : NOT INSERTED : $errorstr";
			mysqli_rollback($db);		
			
		//	echo "$errorstr";
			
		}
		$query="select rollno from students_main where rollno='$rollno'";
		$res=mysqli_query($db,$query);
	
		if(mysqli_num_rows($res)==1)
		{
			for($i=6;$i<$len_linearray;$i++)
			{
			
				
				$groupid=trim(strtoupper($linearray[$i]));
				$groupid=strip_tags(mysqli_real_escape_string($db,$groupid));
				if($groupid=="")
					continue;
				$query="insert into students_belongtogroups (rollno,groupid) values ('$rollno','$groupid')";
				
				$res=mysqli_query($db,$query);
				
				if(!($res))
				{
					$query="select * from students_belongtogroups where rollno='$rollno' and groupid='$groupid'";
					$res=mysqli_query($db,$query);
					if(!$res || mysqli_num_rows($res)!=1)
					{
					
				
						$finalstatus.="<br>[$rollno - $groupid] not inserted";
					}
			
				}
			}
			mysqli_commit($db);
		}
		
		;
	    }
		
		
		
		mysqli_close($db);
		//header("Location: ./../student_registration.php?prev=fail&msg=$errorstr");
	} 
	else 
	{
		$finalstatus.="Error opening the file";
	   
	} 
	unlink($filename);
	fclose($handle);
	allend:
	header("Location: ./../upload_studentlist.php?result=$finalstatus");
	//echo $finalstatus;
	
}
?>
