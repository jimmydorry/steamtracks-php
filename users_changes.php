<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users_changes
///////////////
$timestamp = '1392527319682'; //OPTIONAL
//$timestamp = '1392532605039';
$fields = array();
	$fields[] = 'dota2:wins';
	$fields[] = 'dota2:level';
	$fields[] = 'dota2:competitiveRank';
	$fields[] = 'dota2:soloCompetitiveRank';

$users_changes_response = $steamtracks->users_changes($timestamp, $fields);

if(!empty($users_changes_response['result'])){
	echo '<br /><pre>';
	print_r($users_changes_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_changes_response);
}

?>