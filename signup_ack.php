<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//signup_ack
///////////////

$token = 'XXXXXXXXXXXXXXXXXXXX'; //TOKEN FROM signup_token, MAKE SURE IT IS VALID
$userid32 = '28755155'; //OPTIONAL
$ack_response = $steamtracks->signup_ack($token, $userid32);

if(!empty($ack_response['result'])){
	//$token = $token_response['result']['token'];
	echo '<br /><pre>';
	print_r($ack_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($ack_response);
}

?>