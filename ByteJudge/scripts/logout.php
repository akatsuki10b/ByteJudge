<?php
session_start();
$_SESSION=array();
session_destroy();
setcookie('PHPSESSID','',time()-100);
header("Location: ./../login.php?loggedout=true");
?>
