<?php
require_once('./functions.php');
require_once('./connections/parameters.php');

$steamtracks = new steamtracks($steamtracks_api_key, $steamtracks_api_secret, false);

///////////////
//users
///////////////

$page = 1; //OPTIONAL

if(isset($_GET['page']) && is_numeric($_GET['page'])){
	$page = $_GET['page'];
}
else{
	header("Location: ./users.php?page=1");
}

$users_response = $steamtracks->users($page);

if(!empty($users_response['result'])){
	echo '<br /><pre>';
	print_r($users_response['result']);
	echo '</pre><br /><br />';
}
else{
	var_dump($users_response);
}

?>