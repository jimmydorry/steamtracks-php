<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_info
///////////////
$user = '28755155'; //required
$users_info_response = $steamtracks->users_info($user);

if(!empty($users_info_response['result'])){
	echo '<br /><pre>';
	print_r($users_info_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_info_response);
}

?>