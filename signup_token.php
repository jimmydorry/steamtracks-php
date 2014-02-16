<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//signup_token
///////////////

$steamid32 = '28755155'; //OPTIONAL
$return_steamid32 = 'true'; //OPTIONAL
$token_response = $steamtracks->signup_token($steamid32, $return_steamid32);

if(!empty($token_response['result']['token'])){
	$token = $token_response['result']['token'];
	echo '<br /><a href="https://steamtracks.com/appauth/'.$token.'">https://steamtracks.com/appauth/'.$token.'</a><br /><br />';
}
else{
	var_dump($token_response);
}

?>