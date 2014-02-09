<?php
require_once('./connections/parameters.php');

if (!function_exists("steamtracks_curl")) {
function steamtracks_curl($method, $reqest_type = 'GET', $getfields = array(), $postfields = array(), $verify_ssl = FALSE){
	//WE HAVE TO TURN VERIFICATION OFF BECAUSE FOR SOME REASON THE SSL ON THE API DOES NOT MATCH
	
	global $steamtracks_api_key;
	global $steamtracks_api_secret;
	
	//if(!is_array($getfields)) $getfields = array();
	//if(!is_array($postfields)) $postfields = array();
	
	$url = "https://steamtracks.com/api/v1/"; 
	$api_url = $url . $method;
	
	//WE REQUIRE A CHANGING BASE FOR OUR SIGNATURE
	$postfields['t'] = time();
	
	//TURN ARRAY INTO JSON STRING
	$data_string = json_encode($postfields);
	
	//WE NEED A API SIGNATURE
	$api_sig = urlencode(base64_encode(hash_hmac('sha1', $data_string, $steamtracks_api_secret, 1)));
	
	//HANDLE THE GET FIELDS
	if($reqest_type == 'GET'){
		$getfields[] = 'payload=' . urlencode($data_string);
		$getfields_url = implode('&', $getfields);
	}
	else if($reqest_type == 'POST'){
		$postfields['payload'] = $data_string;
	}
	
	//CONSTRUCT THE FINAL URL
	if($reqest_type == 'GET'){
		$api_url = $api_url . '?' . $getfields_url;
	}
	
	echo 'API URL: '.$api_url.'<hr />';

	echo 'GET: '; print_r($getfields); echo '<hr />';
	echo 'POST: '; print_r($postfields); echo '<hr />';
	
	//SET THE REQUIRED HEADERS
	$headers = array( 
		"SteamTracks-Key: " . $steamtracks_api_key,
		"SteamTracks-Signature: " . $api_sig,
		"ACCEPT: application/json",
		"Content-Type: application/json" 
	); 
	
	//SET THE REQUIRED CURL OPTIONS
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, $api_url); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 60); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	
	//DONT CHECK SSL CERT BY DEFAULT
	if(!$verify_ssl){ 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	}
	
	//HANDLE THE POST FIELDS
	if($reqest_type == 'POST'){
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	}
	
	//SEND OFF THE CURL REQUEST
	$data = curl_exec($ch); 
	
	//CATCH ERROR OR RETURN ARRAY
	if (curl_errno($ch)) { 
		$data = "Error: " . curl_error($ch); 
	}
	else{
		$data = json_decode($data, 1);
	}

	curl_close($ch);
	
	return $data;
}
}

?>