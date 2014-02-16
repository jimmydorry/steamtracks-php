<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_count
///////////////
$users_count_response = $steamtracks->users_count();

if(!empty($users_count_response['result'])){
	echo '<br /><pre>';
	print_r($users_count_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_count_response);
}

?>