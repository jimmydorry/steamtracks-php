<?php
require_once('functions.php');

if(isset($_GET['steamid32']) && is_numeric($_GET['steamid32'])){
	$steam_id = $_GET['steamid32'];
}
else{
	$steam_id = 28755155;
}

if(isset($_GET['token'])){
	$token = $_GET['token'];
}

//$db = new dbWrapper($hostname, $username, $password, $database, false);

if(!empty($token) && !empty($steam_id)){
	$accept_request_postfields = array(
							'token' => $token,
							'user' => $steam_id);
	$accept_request = steamtracks_curl('signup/ack', 'POST', NULL, $accept_request_postfields);
	
	var_dump($accept_request);
}
else{
	echo 'Missing steam_id or token!!';
}


?>