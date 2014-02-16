<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_games
///////////////
$users_games_response = $steamtracks->users_games();

if(!empty($users_games_response['result'])){
	echo '<br /><pre>';
	print_r($users_games_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_games_response);
}

?>