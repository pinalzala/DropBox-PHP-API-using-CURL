# DropBox-PHP-SDK-using-CURL
DropBox API for PHP is an open source that allows PHP applications to interact programmatically with the Dropbox REST API using CURL.   It supports operations such as creating, updating, uploading, downloading and deleting files and folders.



System Requirements:

  PHP 5 or Above (I tested with 5.5.9)  
  CURL extension for PHP  
  SSL CA Bundle    

How to install manually:

  Clone project
  Edit "functions.php" and put your Client ID, Secret Key and oAuth callback URL in relevant places.
  Require "functions.php", create an object and start calling functions!
  
How to get the example running:  

  Deploy to your web server 
  Make sure the file "tokens" is read+writable(Set Read/Write permission to file) by your web user.  
  Edit "functions.php" and include your Live Client ID, Secret Key and oAuth callback URL in relevant places. 
  Run "mytest.php" and follow the step to login with DropBox!  
