<?php
//Developed by Pinal Zala
include_once 'functions.php'; 

$token = dropbox_tokenstore::acquire_token();  // Call this function to grab a current access_token, or false if none is available.
if (!$token) 
{ 

} else 
{
$folder_id  =$_GET['folder_id']; 
$target_dir = "";  // for example you are taking image from the upload directory
$target_file = $target_dir . basename('1.jpg'); // for example if file name is 1.png write 1.png in file nanme

	$api_url = 'https://content.dropboxapi.com/2/files/upload'; //dropbox api url
	
        $headers = array('Authorization: Bearer '. $token,
            'Content-Type: application/octet-stream',
            'Dropbox-API-Arg: '.
            json_encode(
                array(
                    "path"=> $folder_id."/". basename('1.jpg'),
                    "mode" => "add",
                    "autorename" => true,
                    "mute" => false
                )
            )

        );

        $ch = curl_init($api_url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        $fp = fopen($target_file, 'rb');
        $filesize = filesize($target_file);

        curl_setopt($ch, CURLOPT_POSTFIELDS, fread($fp, $filesize));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
		print_r($response); 
		
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($httpcode == "200") {

		return json_decode($response, true);
		} else {
			return array('error' => 'HTTP status code not expected - got ', 'description' => $httpcode);
		}

    curl_close($ch);
		

}

?>