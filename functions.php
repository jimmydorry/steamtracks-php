<?php
require_once('./connections/parameters.php');

if (!function_exists("steamtracks_curl")) {
function steamtracks_curl($method, $request_type = 'GET', $parameters = array(), $verify_ssl = FALSE, $debug = FALSE){
	//WE HAVE TO TURN SSL VERIFICATION OFF BECAUSE FOR SOME REASON THE SSL ON THE API DOES NOT MATCH
	
	global $steamtracks_api_key;
	global $steamtracks_api_secret;
	
	$url = "https://steamtracks.com/api/v1/"; 
	$api_url = $url . $method;
	
	//WE REQUIRE A CHANGING BASE FOR OUR SIGNATURE
	$parameters['t'] = time();
	
	//TURN ARRAY INTO JSON STRING
	$data_string = json_encode($parameters);
	
	//WE NEED A API SIGNATURE
	$api_sig = urlencode(base64_encode(hash_hmac('sha1', $data_string, $steamtracks_api_secret, 1)));
	
	//CONSTRUCT THE FINAL URL
	if($request_type == 'GET'){
		$api_url = $api_url . '?payload=' . urlencode($data_string);
	}
	
	if($debug){
		echo 'API url: '.$api_url.'<br />';
		echo 'Payload: '.$data_string.'<br />';
	}
	
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
}

if (!class_exists("dbWrapper")) {
Class dbWrapper {
    protected $_mysqli;
    protected $_debug;
	public $row_cnt;
	public $row_cnt_affected;
 
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

	public function escape($query){
		return $this->_mysqli->real_escape_string($query);
	}
	
	public function multi_query($query){
		if(is_array($query)){
			$exploded = implode(';', $query);
		}
		else{
			$exploded = $query;
		}
		
		if($query = $this->_mysqli->multi_query($exploded)){
			$i = 0; 
			do { 
				$i++; 
			} 
			while ($this->_mysqli->more_results() && $this->_mysqli->next_result()); 
		}
		
		if($this->_mysqli->errno){
			if ($this->_debug) {
				echo mysqli_error($this->_mysqli);
				debug_print_backtrace();
			}
			return false;
		}
		else{
			return true;
		}
	}
	 
    public function q($query) {
        if ($query = $this->_mysqli->prepare($query)) {
            if (func_num_args() > 1) {
                $x = func_get_args(); //grab all of the arguments
                $args = array_merge(array(func_get_arg(1)), 
                    array_slice($x, 2)); //filter out the query part, leaving the type declaration and parameters
                $args_ref = array();
                foreach($args as $k => &$arg) { //not sure what this step is doing
                    $args_ref[$k] = &$arg; 
                }
                call_user_func_array(array($query, 'bind_param'), $args_ref); // bind each parameter in the form of: $query bind_param (param1, param2, etc.)
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
			
			$this->row_cnt = $query->num_rows;				//num rows
			$this->row_cnt_affected = $query->affected_rows;	//affected rows
			
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

?>
