<?php
	session_start();
	define("link", "http://localhost/Mr seyi/", true);
	define("NGN", "&#8358;", true);
		
	include_once("dbconfig.php");
	include_once("users.php");

            if(isset($_POST['search'])) {
                header('location: search/'.$_POST["search"]);
            } 
	
	$GLOBALS['USER'] = $user = $session = $user_logout = $user_logout = new Users();
?>