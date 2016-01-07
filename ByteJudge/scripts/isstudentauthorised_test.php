<?php
//return -1 on error in query, 0 if not authorised to give test, 2 if not time yet, 1 if okay
function isstudentauthorised($db,$rollno,$testid)
{
	//required database connection
	$rollno=strip_tags(mysqli_real_escape_string($db,$rollno));
	$testid=strip_tags(mysqli_real_escape_string($db,$testid));
	$query="select * from (select groupid from students_belongtogroups where rollno='$rollno') as A natural join (select groupid from test_visibleto where testid='$testid') as B where A.groupid=B.groupid";
	//echo $query;
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		return "Error in Query";
	}
	if(mysqli_num_rows($res)==0)
	{
		return "Not Authorised to give test";
	}

	$query="select testid from test_main where testid='$testid' and visiblefrom < CURRENT_TIMESTAMP() ";
	//echo $query;
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		return "Error in Query";
	}
	if(mysqli_num_rows($res)!=1)
	{
		return 2;
	}
	$query="select testid from test_main where testid='$testid' and visibletill>CURRENT_TIMESTAMP()";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		return "Error in Query";
	}
	if(mysqli_num_rows($res)!=1)
	{
		return 3;
	}
	return 1;
}
function isproblemintest($db,$probcode,$testid)
{
	$query="select * from test_problems where testid='$testid' and problem_code='$probcode'";
	$res=mysqli_query($db,$query);
	if(!($res))
	{
		return "Error in Query";	
	}
	if(mysqli_num_rows($res)!=1)
	{
		return 0;
	}
	return 1;
}
?>
