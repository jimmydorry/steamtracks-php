<?php
require_once('./functions.php');

$token_getfields = array('return_steamid32=true');
$token_response = steamtracks_curl('signup/token', 'GET', $token_getfields);

if(!empty($token_response['result']['token'])){
	$token = $token_response['result']['token'];
	echo '<a href="https://steamtracks.com/appauth/'.$token.'">Grant access to Dota MMR</a>';
}
else{
	var_dump($token_response);
}
?>