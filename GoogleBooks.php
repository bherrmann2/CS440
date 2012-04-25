<?php

require_once("./GoogleBooksConf.php");
require_once("./google-api-php-client/src/apiClient.php");
require_once("./google-api-php-client/src/contrib/apiBooksService.php");
require_once("./Book.php");
//Service Account Client ID
//GB_API_CLIENT_ID
//Service Account Name
//GB_API_SERVICE_ACCOUNT_EMAIL
//Key File
//GB_API_KEY_FILE

//add Function to add book to google bookself
//takes a book object as a param
function add_google_book($pBookObject)
{
	$client = new apiClient();
	$client->setApplicationName(GB_API_APP_NAME);
	
	session_start();
	//Set cached access token
	if (isset($_SESSION['gb_api_token']))
	{
		$client->setAccessToken($_SESSION['gb_api_token']);
	}

	//Load key in PKCS 12 format
	$gb_key = file_get_contents(GB_API_KEY_FILE);
	$client->setClientId(GB_API_CLIENT_ID);
	$client->setAssertionCredentials(new apiAssertionCredentials(GB_API_SERVICE_ACCOUNT_EMAIL, array('https://www.googleapis.com/auth/books'), $gb_key));

	$service = new apiBooksService($client);
	$mylib = $service->mylibrary_bookshelves;
	//Book code goes here
	$mylib->addVolume(GB_API_BOOKSHELF_UID, 'HFjLm2BauZ8C', array());
#	$mylib->addVolume(GB_API_BOOKSHELF_UID, $pBookObject->getVolumeID, NULL);
	
	//Update access token
	if($client->getAccessToken())
	{
		$_SESSION['gb_api_token'] = $client->getAccessToken();
	}
}

function search_google_books($pISBN)
{

}

function find_google_book($pISBN, $pAuthor = array(), $pTitle, $pKeywords = array())
{

}

function remove_google_book($pBookObject)
{
	
}

add_google_book(NULL);
?>
Hello World
