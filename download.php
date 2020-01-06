<?php 
//Developed by Pinal Zala
include_once 'functions.php'; 
$token =  dropbox_tokenstore::acquire_token(); 

if (!$token) {
	echo "Error";
} else {
	$sd = new dropbox($token);

	$url = 'https://api.dropboxapi.com/2/files/get_temporary_link';

	$data = '{
		"path": "/1.png"
	}';
	
	
		try {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer '.$token,
		));
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$response = json_decode($output, true);
		$link = $response['link'];
		$data = file_get_contents($link);
		$new = '1.png';
		file_put_contents($new, $data);
		$err = curl_error($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		echo "Image store to server successful"
		} catch (Exception $e) {
			echo "Error: ".$e->getMessage();
		exit;
		}
		
	
}
?>