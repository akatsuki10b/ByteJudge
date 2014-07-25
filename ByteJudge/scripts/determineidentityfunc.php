<?php
function isloggedin($as)
{
	if(session_id()=="")
		session_start();
	if(!(isset($_SESSION['username']) && isset($_SESSION['type']) ))
		return false;
	if($as=="any")
		return true;
	if($as==$_SESSION['type'])
	{
		return true;
	}
	return false;
}

?>
