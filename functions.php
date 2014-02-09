<?php
require_once('./connections/parameters.php');

if (!class_exists("dbWrapper")) {
Class dbWrapper {
    protected $_mysqli;
    protected $_debug;
 
    public function __construct($host, $username, $password, $database, $debug) {
        $this->_mysqli = new mysqli($host, $username, $password, $database);
        $this->_debug = (bool) $debug;
        if (mysqli_connect_errno()) {
            if ($this->_debug) {
                echo mysqli_connect_error();
                debug_print_backtrace();
            }
            return false;
        }
        return true;
    }
 
    public function q($query) {
        if ($query = $this->_mysqli->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_merge(array(func_get_arg(1)),
                    array_slice($x, 2));
                $args_ref = array();
                foreach($args as $k => &$arg) {
                    $args_ref[$k] = &$arg; 
                }
                call_user_func_array(array($query, 'bind_param'), $args_ref);
            }
            $query->execute();
 
            if ($query->errno) {
              if ($this->_debug) {
                echo mysqli_error($this->_mysqli);
                debug_print_backtrace();
              }
              return false;
            }
 
            if ($query->affected_rows > -1) {
                return $query->affected_rows;
            }
            $params = array();
            $meta = $query->result_metadata();
            while ($field = $meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }
            call_user_func_array(array($query, 'bind_result'), $params);
 
            $result = array();
            while ($query->fetch()) {
                $r = array();
                foreach ($row as $key => $val) {
                    $r[$key] = $val;
                }
                $result[] = $r;
            }
            $query->close(); 
            return $result;
        } else {
            if ($this->_debug) {
                echo $this->_mysqli->error;
                debug_print_backtrace();
            }
            return false;
        }
    }
 
    public function handle() {
        return $this->_mysqli;
    }
	
	public function last_index() {
		return $this->_mysqli->insert_id;
	}
}
}

if (!function_exists("curl")) {
function curl($link, $postfields = '', $cookie = '', $refer = '', $user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1'){
	$ch = curl_init($link);
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	if($refer){
		curl_setopt($ch, CURLOPT_REFERER, $refer);
	}
	if($postfields){
		curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	}
	if($cookie){
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
	}
	$page = curl_exec($ch);
	curl_close($ch);
	return $page;
}
}

if (!function_exists("cut_str")) {
function cut_str($str, $left, $right){
	$str = substr ( stristr ( $str, $left ), strlen ( $left ));
	$leftLen = strlen ( stristr ( $str, $right ) );
	$leftLen = $leftLen ? - ($leftLen) : strlen ( $str );
	$str = substr ( $str, 0, $leftLen);
	
	return $str;
}
}

//GIVEN A UNIX TIMESTAMP RETURNS A RELATIVE DISTANCE TO DATE (23.4 days ago)
//PUTTING ANY VALUE IN 2ND VARIABLE MAKES IT RETURN RAW HOURS APART
if(!function_exists('relative_time')){
function relative_time($time, $output = 'default'){
	if(!is_numeric($time)){
		if(strtotime($time)){
			$time = strtotime($time);
		}
		else{
			return FALSE;
		}
	}

	if($output == 'default'){
		if((time() - $time) >= 2592000){
			$time_adj = round(((time() - $time)/2592000), 1) . ' months ago';
		}
		else if((time() - $time) >= 86400){
			$time_adj = round(((time() - $time)/86400), 1) . ' days ago';
		}
		else if((time() - $time) >= 3600){
			$time_adj = round(((time() - $time)/3600), 1) . ' hours ago';
		} 
		else {
			$time_adj = round(((time() - $time)/60), 0) . ' mins ago';
		}
	}
	else{
		$time_adj = round(((time() - $time)/3600), 1);
	}
	
	return $time_adj;
}
}

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