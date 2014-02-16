<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_leavers
///////////////
$users_leavers_response = $steamtracks->users_leavers();

if(!empty($users_leavers_response['result'])){
	echo '<br /><pre>';
	print_r($users_leavers_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_leavers_response);
}

?>