<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_states
///////////////
$users_states_response = $steamtracks->users_states();

if(!empty($users_states_response['result'])){
	echo '<br /><pre>';
	print_r($users_states_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_states_response);
}

?>