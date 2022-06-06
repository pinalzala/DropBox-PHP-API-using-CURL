<?php
//Developed by Pinal Zala
define("client_id2", "Your client Id Here");
define("client_secret2", "Your client secret here");
define("callback_uri2", "Call back url here"); // For Example : http://localhost/dropboxfiver/callback.php
define("dropbox_base_url", "https://content.dropboxapi.com/1/");
define("token_store","tokens");  // Edit path to your token store if required
class dropbox {

	public $access_token = '';
	public function __construct($passed_access_token) {
		$this->access_token = $passed_access_token;
	}
	
	
	// Gets the contents of a DropBox folder.
	// Pass in the ID of the folder you want to get.
	// Or leave the second parameter blank for the root directory
	// Returns an array of the contents of the folder.

	//get folder list
	public function getimage($folderid) {
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://content.dropboxapi.com/2/files/get_thumbnail_batch',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{"entries":[{"format":"png","mode":"strict","path":"'.$folderid.'","size":"w64h64"}]}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$this->access_token,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
return $response = json_decode($response, true);;
	}
	public function get_folder_drop($folderid) {
	
	if ($folderid === null) {
	$data = '{
    "path": "",
    "recursive": true,
    "include_media_info": false,
    "include_deleted": true,
    "include_has_explicit_shared_members": false,
    "include_mounted_folders": false
	}';
	}else{
	$path = $folderid; 	
		
	$data = '{
    "path": "'.$path.'",
    "recursive": true,
    "include_media_info": false,
    "include_deleted": true,
    "include_has_explicit_shared_members": false,
    "include_mounted_folders": false
	}';
		}
		try {
	    $url = 'https://api.dropboxapi.com/2/files/list_folder';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$this->access_token,
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		} catch (Exception $e) {
		}
		if ($httpcode == "200") {
			$response = json_decode($output, true);
		} else {
			$response = array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}
		
		if (array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
					
			$arraytoreturn = Array();
			$temparray = Array();
			if (@$response['paging']['next']) {
				parse_str($response['paging']['next'], $parseout);
				$numerical = array_values($parseout);
			}
			if (@$response['paging']['previous']) {
				parse_str($response['paging']['previous'], $parseout1);
				$numerical1 = array_values($parseout1);
			}			
			foreach ($response as $subarray) {
			if(is_array($subarray) || is_object($subarray)){
				foreach ($subarray as $item) {
					//print_r($item);
					if (@array_key_exists('id', $item)) {
						if(isset($item['path_display'])){
							$type=$item['path_display'];
						}else{
							$type ="";
						}
						if(isset($item['size'])){ 
							$size=$item['size'];
						}else{
							$size ="";
						}
						array_push($temparray, Array('name' => $item['name'], 'id' => $item['id'], 'type' => $item['.tag'], 'size' => $size, 'source' => $type));
					}
				}
			}
			}
			$arraytoreturn['data'] = $temparray;
			if (@$numerical[0]) {
				if (@$numerical1[0]) {
					$arraytoreturn['paging'] = Array('previousoffset' => $numerical1[0], 'nextoffset' => $numerical[0]);
				} else {
					$arraytoreturn['paging'] = Array('previousoffset' => 0, 'nextoffset' => $numerical[0]);		
				}			
			} else {
				$arraytoreturn['paging'] = Array('previousoffset' => 0, 'nextoffset' => 0);
			}
			return $arraytoreturn;
		}
	}
	


	// Gets a pre-signed (public) direct URL to the item
	// Pass in a file ID
	// Returns a string containing the pre-signed URL.

	public function get_source_link($fileid) {

         echo $fileid;
		$response = $this->get_file_properties($fileid);
		print_r($responses);exit;
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			return $response['source'];
		}
	}
	
		// Gets a shared edit (read-write) link to the item.

	function get_shared_edit_link($fileid) {
		$response = curl_get(dropbox_base_url.$fileid."/shared_edit_link?access_token=".$this->access_token);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {	
			return $response['link'];
		}
	}

	// Gets the remaining quota of your dropbox account.
	// Returns an array containing your total quota and quota available in bytes.

	function get_quota() {
		
		
	$token1	= $this->access_token;
	$headers = array("Authorization: Bearer ".$token1,
                 "Content-Type: application/json");

	$ch = curl_init('https://api.dropboxapi.com/2/users/get_space_usage');
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "null");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response1 = curl_exec($ch);

	curl_close($ch);
    $response = json_decode($response1);
	if(!empty($response)){
	return $response;
	}else{
	return false;
	}
		
	}
	
	// Deletes file.

	function delete_object_drop($fileid) {
	$token1	= $this->access_token;
	$url = 'https://api.dropboxapi.com/2/files/delete_v2';

	$data = '{
    "path": "'.$fileid.'"
	}';
		try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token1,
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$err = curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		} catch (Exception $e) {
		}
		if ($httpcode == "200") {
			return json_decode($output, true);
		} else {
		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}	
	}
	
	// Downloads a file from DropBox to the server.
	// Pass in a file ID.
	// Returns a multidimensional array:
	// ['properties'] contains the file metadata and ['data'] contains the raw file data.
	
	//download file
	public function download_drop($fileid) {
		$props = $this->get_file_properties($fileid);
		$response = $this->curl_get("https://content.dropboxapi.com/2/".$fileid."/content?access_token=".$this->access_token, "false", "HTTP/1.1 302 Found");

		
		$arraytoreturn = Array();
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			array_push($arraytoreturn, Array('properties' => $props, 'data' => $response));
			return $arraytoreturn;
		}		
	}

	
	// Uploads a file from disk.
	// Pass the $folderid of the folder you want to send the file to, and the $filename path to the file.
	// Also use this function for modifying files, it will overwrite a currently existing file.

	function put_file($folderid, $filename) {
		$r2s = dropbox_base_url.$folderid."/files/".basename($filename)."?access_token=".$this->access_token;
		$response = $this->curl_put($r2s, $filename);
		print_r($response);exit;
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);

			exit;
		} else {
			return $response;
		}
			
	}
	
		
	/**
	 * Upload file directly from remote URL
	 * 
	 * @param string $sourceUrl - URL of the file
	 * @param string $folderId - folder you want to send the file to
	 * @param string $filename - target filename after upload
	 */

	//upload file
	function put_file_from_url($sourceUrl, $folderId, $filename){
		$r2s = dropbox_base_url.$folderId."/files/".$filename."?access_token=".$this->access_token;
		
		$chunkSizeBytes = 1 * 1024 * 1024; //1MB
		
		//download file first to tempfile
		$tempFilename = tempnam("/tmp", "UPLOAD");
		$temp = fopen($tempFilename, "w");
		$handle = @fopen($sourceUrl, "rb");
		if($handle === FALSE){
			throw new Exception("Unable to download file from " . $sourceUrl);
		}
		
		while (!feof($handle)) {
			$chunk = fread($handle, $chunkSizeBytes);
			fwrite($temp, $chunk);
		}		
		
		fclose($handle);
		fclose($temp);
		
		//upload to DropBox
		$response = $this->curl_put($r2s, $tempFilename);
		if (@array_key_exists('error', $response)) {
			throw new Exception($response['error']." - ".$response['description']);
			exit;
		} else {
			unlink($tempFilename);
			return $response;
		}
	}	
	
	
	// Creates a folder.
	// Pass $folderid as the containing folder (or 'null' to create the folder under the root).
	// Also pass $foldername as the name for the new folder and $description as the description.
	// Returns the new folder metadata or throws an exception.
	

	/// create folder
	function create_folder_drop($folderid, $foldername, $description="") {
	$token1	= $this->access_token;		
		
if ($folderid === null) {
	$data = '{
    "path": "/'.$foldername.'",
    "autorename": false
}';
	}else{

	$path = $folderid; 	
		
	$data = '{
    "path": "'.$path.'/'.$foldername.'",
    "autorename": false
}';
	}
	
	try {
		$url = 'https://api.dropboxapi.com/2/files/create_folder_v2';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token1
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		   print_r($response);exit;
		$err = curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		} catch (Exception $e) {
		}
		if ($httpcode == "200") {
		return json_decode($output, true);
		} else {
		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}
	
	}
	 

	 //rename folder name
	function rename_file_folder_drop($folderid, $foldername, $path, $paren_id) {
	$token	= $this->access_token;
	
	$from_path =$path;
	$to_path =$paren_id."/".$foldername;
	
	$data1 = '{
    "from_path": "'.$from_path.'",
    "to_path": "'.$to_path.'",
    "allow_shared_folder": false,
    "autorename": true,
    "allow_ownership_transfer": false
}';
	
	
	try {
		$url = 'https://api.dropboxapi.com/2/files/move';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data1);
		$output = curl_exec($ch);
		$err = curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		} catch (Exception $e) {
		}
		if ($httpcode == "200") {
		return json_decode($output, true);
		} else {

		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}
	}
	
	function move_file_folder_drop($from_path, $to_path) {
	
	$data = '{
    "from_path": "'.$from_path.'",
    "to_path": "'.$to_path.'",
    "allow_shared_folder": false,
    "autorename": false,
    "allow_ownership_transfer": false
}';
	try {
		$url = 'https://api.dropboxapi.com/2/files/move_v2';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token
		));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$err = curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
		} catch (Exception $e) {
		}
		if ($httpcode == "200") {
		return json_decode($output, true);
		} else {
		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}	
	}

	// *** PROTECTED FUNCTIONS ***
	
	// Internally used function to make a GET request to DropBox.
	// Functions can override the default JSON-decoding and return just the plain result.
	// They can also override the expected HTTP status code too.
	
	protected function curl_get($uri, $json_decode_output="true", $expected_status_code="HTTP/1.1 200 OK") {
		$output = "";
		$output = @file_get_contents($uri);
		if ($http_response_header[0] == $expected_status_code) {
			if ($json_decode_output == "true") {
				return json_decode($output, true);
			} else {
				return $output;
			}
		} else {
			return Array('error' => 'HTTP status code not expected - got ', 'description' => substr($http_response_header[0],9,3));
		}
	}

	// Internally used function to make a POST request to DropBox.

	protected function curl_post($uri, $inputarray, $access_token) {
		$trimmed = json_encode($inputarray);
		try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$access_token,
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $trimmed);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		} catch (Exception $e) {
		}
		
		if ($httpcode == "201") {
			return json_decode($output, true);
		} else {
			return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}
	}


	// Internally used function to make a PUT request to DropBox.

	protected function curl_put($uri, $fp) {
	  $output = "";
	  try {
	  	$pointer = fopen($fp, 'r+');
	  	$stat = fstat($pointer);
	  	$pointersize = $stat['size'];
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_PUT, true);
		curl_setopt($ch, CURLOPT_INFILE, $pointer);
		curl_setopt($ch, CURLOPT_INFILESIZE, (int)$pointersize);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
		
		//HTTP response code 100 workaround
		//see http://www.php.net/manual/en/function.curl-setopt.php#82418
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));			
		
		$output = curl_exec($ch);
		print_r($output);
		echo $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE); die;
	  } catch (Exception $e) {
	  }
	  	if ($httpcode == "200" || $httpcode == "201") {
	  		return json_decode($output, true);
	  	} else {
	  		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
	  	}
		
	}

	// Internally used function to make a DELETE request to DropBox.
	protected function curl_delete($uri) {
	  $output = "";
	  try {
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');    
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 4);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
		$output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	  } catch (Exception $e) {
	  }
	  	if ($httpcode == "200") {
	  		return json_decode($output, true);
	  	} else {
	  		return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
	  	}
	}
	

}

class dropbox_auth {

	// build_oauth_url() - Request to authorization	for build token
	// Builds a URL for the user to log in to DropBox and get the authorization code, which can then be
	// passed onto get_oauth_token to get a valid oAuth token.

	
	public static function build_oauth_url() {
		$response = "https://www.dropbox.com/oauth2/authorize?client_id=".client_id2."&token_access_type=offline&response_type=code&state=12345&redirect_uri=".urlencode(callback_uri2);
		return $response;
	}

	// get_oauth_token()

	// Obtains an oAuth token
	// Pass in the authorization code parameter obtained from the inital callback.
	// Returns the oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).

	public static function get_oauth_token($auth) {
		$arraytoreturn = array();
		$output = "";
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/oauth2/token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/x-www-form-urlencoded',
				));
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		

			$data = "client_id=".client_id2."&redirect_uri=".urlencode(callback_uri2)."&client_secret=".urlencode(client_secret2)."&code=".$auth."&grant_type=authorization_code";	
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$output = curl_exec($ch);
		} catch (Exception $e) {
		}
	
		$out2 = json_decode($output, true);

		$arraytoreturn = Array('access_token' => $out2['access_token'], 'refresh_token' => $out2['refresh_token'], 'expires_in' => $out2['account_id']);
		return $arraytoreturn;
	}
	
	
	// refresh_oauth_token()
	
	// Attempts to refresh an oAuth token
	// Pass in the refresh token obtained from a previous oAuth request.
	// Returns the new oAuth token and an expiry time in seconds from now (usually 3600 but may vary in future).
	

	
	
}

class dropbox_tokenstore {

	// acquire_token()
	
	// Will attempt to grab an access_token from the current token store.
	// If there isn't one then return false to indicate user needs sending through oAuth procedure.
	// If there is one but it's expired attempt to refresh it, save the new tokens and return an access_token.
	// If there is one and it's valid then return an access_token.
	
	
	public static function acquire_token() {
		
		$response = dropbox_tokenstore::get_tokens_from_store();
		if($response['access_token'])
		{
			if(time() < $response['access_token_expires'])
		{
			return $response['access_token'];

		}
		else
		{
			return dropbox_tokenstore::refresh_oauth_token($response['refresh_token']);
		}
		}
		
		/*if (empty($response['access_token'])) {	// No token at all, needs to go through login flow. Return false to indicate this.
			return false;
			exit;
		} else {
				return $response['access_token']; // Token currently valid. Return it.
				exit;
			
		}*/
	}
	
		public static function refresh_oauth_token($refresh) {
		$arraytoreturn = array();
		$output = "";
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://api.dropboxapi.com/1/oauth2/token");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/x-www-form-urlencoded',
				));
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);		

			$data = "client_id=".client_id2."&client_secret=".urlencode(client_secret2)."&refresh_token=".$refresh."&grant_type=refresh_token";	
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$output = curl_exec($ch);
		} catch (Exception $e) {
		}
	
		$out2 = json_decode($output, true);
		$arraytoreturn = Array('access_token' => $out2['access_token'], 'refresh_token' => $refresh, 'expires_in' => $out2['expires_in']);


		$tokentosave = Array();
		$tokentosave = Array('access_token' => $out2['access_token'], 'refresh_token' => $refresh, 'access_token_expires' => (time()+(int)$out2['expires_in']));
		if (file_put_contents(token_store, json_encode($tokentosave))) {
			return true;
		} else {
			return false;
		}
		return $arraytoreturn;
	}
	// get_tokens_from_store()
	// save_tokens_to_store()
	// destroy_tokens_in_store()
	// These functions provide a gateway to your token store.
	// In it's basic form, the tokens are written simply to a file called "tokens" in the current working directory, JSON-encoded.
	// You can edit the location of the token store by editing the DEFINE entry on line 28.
	
	
	
	public static function get_tokens_from_store() {
		$response = json_decode(file_get_contents(token_store), TRUE);
		return $response;
	}
	
	public static function save_tokens_to_store($tokens) {
		$tokentosave = Array();
		$tokentosave = Array('access_token' => $tokens['access_token'], 'refresh_token' => $tokens['refresh_token'], 'access_token_expires' => (time()+(int)$tokens['expires_in']));
		if (file_put_contents(token_store, json_encode($tokentosave))) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function destroy_tokens_in_store() {
		if (file_put_contents(token_store, "loggedout")) {
			return true;
		} else {
			return false;
		}
		
	}
	
}

?>

