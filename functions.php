<?php
if(!class_exists('steamtracks')){
class steamtracks{
	private $steamtracks_api_key;
	private $steamtracks_api_secret;
	private $verify_ssl;
	private $debug;
	
	private $api_url = "https://steamtracks.com/api/v1/";
	
	function __construct($steamtracks_api_key, $steamtracks_api_secret, $verify_ssl = FALSE, $debug = FALSE){
		$this->steamtracks_api_key = $steamtracks_api_key;
		$this->steamtracks_api_secret = $steamtracks_api_secret;
		$this->verify_ssl = $verify_ssl;
		$this->debug = $debug;
	}
	
	//request is either GET or POST
	private function curl_do($request_type, $method, $data_string){
		$api_url = $this->api_url . $method;
		if($request_type == 'GET'){
			$api_url .= '?payload=' . urlencode($data_string);
		}

		$api_sig = urlencode(base64_encode(hash_hmac('sha1', $data_string, $this->steamtracks_api_secret, 1)));

		if($this->debug){
			echo 'API url: '.$api_url.'<br />';
			echo 'Payload: '.$data_string.'<br />';
		}

		//SET THE REQUIRED HEADERS
		$headers = array( 
			"SteamTracks-Key: " . $this->steamtracks_api_key,
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
		if(!$this->verify_ssl){ 
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0); 
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		}
		
		//HANDLE THE POST FIELDS
		if($request_type == 'POST'){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
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
	
	function notify($message, $to = NULL, $broadcast = FALSE, $exclude_offline = FALSE){
		if(!empty($to) || !empty($broadcast)){
			$parameters = array();
			$parameters['t'] = time();
			if(!empty($to)) $parameters['to'] = $to;
			if(!empty($broadcast)) $parameters['broadcast'] = (string) $broadcast;
			if(!empty($exclude_offline)) $parameters['exclude_offline'] = (string) $exclude_offline;
			
			$data = $this->curl_do('GET', 'signup/token', json_encode($parameters));
		}
		else{
			$data = 'Must have either a "to" set or "brodcast" set to true.<br />';
		}
		return $data;
	}
	
	function signup_token($steamid32 = NULL, $return_steamid32 = FALSE){
		$parameters = array();
		$parameters['t'] = time();
		if(!empty($steamid32)) $parameters['steamid32'] = $steamid32;
		if(!empty($return_steamid32)) $parameters['return_steamid32'] = (string) $return_steamid32;
		
		$data = $this->curl_do('GET', 'signup/token', json_encode($parameters));
		return $data;
	}
	
	function signup_stats($token){
		if(!empty($token)){
			$parameters = array();
			$parameters['t'] = time();
			$parameters['token'] = $token;
	
			$data = $this->curl_do('GET', 'signup/status', json_encode($parameters));
		}
		else{
			$data = 'No token given!<br />';
		}
		return $data;
	}
	
	function signup_ack($token, $user = NULL){
		if(!empty($token)){
			$parameters = array();
			$parameters['t'] = time();
			$parameters['token'] = $token;
			if(!empty($user)) $parameters['user'] = (string) $user;
	
			$data = $this->curl_do('POST', 'signup/ack', json_encode($parameters));
		}
		else{
			$data = 'No token given!<br />';
		}
		return $data;
	}

	function users($page = NULL){
		$parameters = array();
		$parameters['t'] = time();
		if(!empty($page)) $parameters['page'] = (string) $page;

		$data = $this->curl_do('GET', 'users', json_encode($parameters));
		return $data;
	}

	function users_count(){
		$parameters = array();
		$parameters['t'] = time();

		$data = $this->curl_do('GET', 'users/count', json_encode($parameters));
		return $data;
	}
	
	function users_info($user){
		if(!empty($user)){
			$parameters = array();
			$parameters['t'] = time();
			$parameters['user'] = (string) $user;
	
			$data = $this->curl_do('GET', 'users/info', json_encode($parameters));
		}
		else{
			$data = 'No user given!<br />';
		}
		return $data;
	}
	
	function users_states(){
		$parameters = array();
		$parameters['t'] = time();
	
		$data = $this->curl_do('GET', 'users/states', json_encode($parameters));
		return $data;
	}
	
	function users_games(){
		$parameters = array();
		$parameters['t'] = time();
	
		$data = $this->curl_do('GET', 'users/games', json_encode($parameters));
		return $data;
	}
	
	function users_leavers(){
		$parameters = array();
		$parameters['t'] = time();
	
		$data = $this->curl_do('GET', 'users/leavers', json_encode($parameters));
		return $data;
	}
	
	function users_flushleavers(){
		$parameters = array();
		$parameters['t'] = time();
	
		$data = $this->curl_do('POST', 'users/flushleavers', json_encode($parameters));
		return $data;
	}
	
	function users_changes($from_timestamp = NULL, $fields = array()){
		$parameters = array();
		$parameters['t'] = time();
		if(!empty($from_timestamp)) $parameters['from_timestamp'] = (string) $from_timestamp;
		if(!empty($fields)) $parameters['fields'] = $fields;
	
		$data = $this->curl_do('GET', 'users/changes', json_encode($parameters));
		return $data;
	}
}
}
?>