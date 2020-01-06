<?php 
//Developed by Pinal Zala
//This test file for first time auth
include_once 'functions.php'; 

$token = dropbox_tokenstore::acquire_token(); 

if (!$token) { ?>
<a href="<?php echo dropbox_auth::build_oauth_url(); ?>">Sync your Dropbox</a>
<?php } else {
	
	// Get Folder Content -  !!! WORKING !!!
	$sd = new dropbox($token);
	$response = $sd->get_folder_drop(null);
	
	

	// Download existing file from Dropbox to local directory
	// This file is in Dropbox root directory
	$remotefile = "test.txt";
	foreach ($response['data'] as $item) {
		
	if ($item['type'] == 'folder' || $item['type'] =='album') {
	// echo 'id==='.$item['id']. 'name--- > '.$item['name'];
	}else{?>
	Click on file name to download	<a href="download.php?id=<?php echo $item['id']; ?>"><?php echo $item['name']; ?></a> </br> 
	<?php } }


	// Create a new Folder under root directory on Dropbox
	$newfolder_name = "uploadtest";
	
	$response = $sd->create_folder_drop(null, $newfolder_name, 'Description'); 
	
	
	// Upload local file to the new created folder (uploadtest)
	// This file is in the same ftp folder with this script
	$localfile = "1.jpg"; ?>
	</br>
	<a href="upload.php?folder_id=id:m76k86xUcmwAAAAAAAAXLg">Click here to upload</a> 



	<p><?php $quota =  $sd->get_quota();
		 $allocated = $quota->allocation->allocated;
		 $used = $quota->used; ?>
		<span>Allocated : <?php echo $allocated; ?> bytes</span></br>
		<span>Used : <?php echo $used; ?> bytes</span></br>
		<span>Remaining : <?php echo $allocated - $used; ?> bytes</span>
	</p>
	


<?php }
?>