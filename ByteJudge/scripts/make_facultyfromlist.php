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
	if(isset($_FILES['faculty_file']) && $_FILES['faculty_file']['size']>0)
	{
		
		
		$filename=$_FILES['faculty_file']['name'];
		if(move_uploaded_file($_FILES['faculty_file']['tmp_name'],$filename))
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
		$facultyid='';
		$fullname='';
		$dob='';
		$emailid='';
		$college='';
		$branch='';
		$designation='';
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
			$facultyid=strip_tags(mysqli_real_escape_string($db,$linearray[0]));
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
		if(isset($linearray[6]))
			$designation=strip_tags(mysqli_real_escape_string($db,$linearray[6]));
		$facultyid=strtoupper($facultyid);
		
		$password=$facultyid;
		if($facultyid=='' || $fullname=='' || $password=='')
		{
			$errorstr="not enough details to create new student";
			$status=false;
			goto end;
		}
	
		
		$query="select userid from users where userid='$facultyid'";
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
			$errorstr= "user by userid $facultyid already exists";
			$status=false;
			goto end;
		}
		//convert dob to correct format
	
		$query="insert into users(userid,type) values('$facultyid','student')";
		
		$res=mysqli_query($db,$query);
		if(!($res))
		{
			$status=false;
			$errorstr="Couldn't insert data in main table";
			goto end;
		}
		$query="insert into faculty_main(facultyid,fullname,dob,emailid,college,branch,designation,count_problemsadded) values('$facultyid','$fullname','$dob','$emailid','$college','$branch','$designation',0)";
		
		$res=mysqli_query($db,$query);
		if(!($res))
		{

			$status=false;
			$errorstr="Couldn't insert data";
			$errorstr=mysqli_error($db);
			goto end;
		}
		$query="insert into logininfo(username,password,createdon) values('$facultyid',SHA1('$password'),CURRENT_TIMESTAMP())";
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
			$finalstatus.="<br><br>FACULTY ID : $facultyid : Inserted successfully";
			
			mysqli_commit($db);
			
			
		}
		else
		{
			$finalstatus.="<br><br>FACULTY ID : $facultyid : NOT INSERTED : $errorstr";
			mysqli_rollback($db);		
			
		//	echo "$errorstr";
			
		}
		$query="select facultyid from faculty_main where facultyid='$facultyid'";
		$res=mysqli_query($db,$query);
	
		if(mysqli_num_rows($res)==1)
		{
			for($i=7;$i<$len_linearray;$i++)
			{
			
				
				$groupid=trim(strtoupper($linearray[$i]));
				$groupid=strip_tags(mysqli_real_escape_string($db,$groupid));
				if($groupid=="")
					continue;
				$query="insert into faculty_belongtogroups (facultyid,groupid) values ('$facultyid','$groupid')";
				
				$res=mysqli_query($db,$query);
				
				if(!($res))
				{
					$query="select * from faculty_belongtogroups where facultyid='$facultyid' and groupid='$groupid'";
					$res=mysqli_query($db,$query);
					if(!$res || mysqli_num_rows($res)!=1)
					{
					
				
						$finalstatus.="<br>[$facultyid - $groupid] not inserted: $groupid doesn't exist";
			
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
	header("Location: ./../upload_facultylist.php?result=$finalstatus");
	//echo $finalstatus;
	
}
?>
