<?php

$errorstr='';
$status=true;
if(!(isset($_GET['adminid'])))
{
	$errorstr="No Admin ID provided";
	$status=false;
	goto end;
}

include('./includes/mysql_login_view.php');
$adminid='';

$adminid=strip_tags(mysqli_real_escape_string($db,$_GET['adminid']));
$adminid=strtoupper($adminid);
$query="select * from admin_main where adminid='$adminid'";
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
	$errorstr="No admin by adminid $adminid";
	goto end;
}
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

$res=mysqli_fetch_array($res);
$fullname=$res['fullname'];


?>
<div id='profile'>
<h2><?php echo "$fullname's"; ?> Profile</h2>
</div>
<table class='profile_table' >
	<thead>
    	<tr>
     	 	<th class="left"></th>
      		<th class="right"></th>
    	</tr>
  	</thead>
  	<tbody>
		<tr>
	 		<td class="left">
				Admin ID. :
	 		</td>
			<td class="right">
	  			<a href='#'><?php echo "$adminid"; ?></a>
	 		</td>
		</tr>
	
		<tr>
	 		<td class="left">
				Name :
	 		</td>
			<td class="right">
	  			<?php echo "$fullname"; ?>
	 		</td>
		</tr>
		
	</tbody>
</table>


<?php
end:
if(!$status)
{
	echo $errorstr;
}
?>
