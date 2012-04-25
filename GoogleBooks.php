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
	$client->setAssertionCredentials(new apiAssertionCredentials("1013677779152@developer.gserviceaccount.com", array('https://www.googleapis.com/auth/books'), $gb_key));
	$client->setClientId(GB_API_CLIENT_ID);

	$service = new apiBooksService($client);
	$mylib = $service->mylibrary_bookshelves;
/*	$bookshelves = $service->bookshelves;

	$pubshelves = $bookshelves->listBookshelves(GB_API_USER_UID, array());
	foreach ($pubshelves as $oneshelf)
	{
		echo $oneshelf;
	}
 */
	//Book code goes here
	/*$shelves = $mylib->listMylibraryBookshelves(array());
	foreach($shelves->getItems() as $item)
	{
		echo $item->getTitle();
	}
	 */
	$shelf2 = $mylib->get(2, array());
	echo $shelf2->getVolumeCount();
	#	echo $mylib->addVolume(2, 'HFjLm2BauZ8C', array());
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
