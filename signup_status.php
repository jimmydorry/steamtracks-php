<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//signup_status
///////////////

$token = 'XXXXXXXXXXXXXXXXXXXX'; //TOKEN FROM signup_token, MAKE SURE IT IS VALID
$status_response = $steamtracks->signup_stats($token);

if(!empty($token_response['result'])){
	//$token = $token_response['result']['token'];
	echo '<br /><pre>';
	print_r($token_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($status_response);
}

?>