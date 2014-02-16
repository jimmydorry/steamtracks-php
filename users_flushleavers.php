<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_flushleavers
///////////////
$users_flushleavers_response = $steamtracks->users_flushleavers();

if(!empty($users_flushleavers_response['result'])){
	echo '<br /><pre>';
	print_r($users_flushleavers_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_flushleavers_response);
}

?>