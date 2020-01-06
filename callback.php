<?php
//Developed by Pinal Zala
require_once "functions.php";
$response = dropbox_auth::get_oauth_token($_GET['code']);
if (dropbox_tokenstore::save_tokens_to_store($response)) {
	header("Location: mytest.php");
} else {
	echo "error";
}
?>