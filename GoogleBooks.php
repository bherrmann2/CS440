<?php

/*
 * CS 440 UIC Spring 2012
 * Walter Dworak
 * walter@wdworak.homelinux.com
 *
 * Use at your own risk
*/

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
class GoogleBooks
{

	public function add($pBookObject)
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
		$client->setAssertionCredentials(new apiAssertionCredentials(GB_API_SERVICE_ACCOUNT_EMAIL, array('https://www.googleapis.com/auth/books'), $gb_key));
		$client->setClientId(GB_API_CLIENT_ID);

		$service = new apiBooksService($client);
		$mylib = $service->mylibrary_bookshelves;
		//Book code goes here
		#$shelf2 = $mylib->get(GB_API_BOOKSHELF_UID, array());
		#echo $shelf2->getVolumeCount();
		#echo $mylib->addVolume(2, 'HFjLm2BauZ8C', array());
		#if(!$mylib->addVolume(GB_API_BOOKSHELF_UID, $pBookObject->getVolumeID, array()))
		$mylib->addVolume(GB_API_BOOKSHELF_UID, $pBookObject->getVolumeID(), array());

		//Update access token
		if($client->getAccessToken())
		{
			$_SESSION['gb_api_token'] = $client->getAccessToken();
		}

		return 1;
	}

	public function search($pISBN)
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
		$client->setAssertionCredentials(new apiAssertionCredentials(GB_API_SERVICE_ACCOUNT_EMAIL, array('https://www.googleapis.com/auth/books'), $gb_key));
		$client->setClientId(GB_API_CLIENT_ID);

		$service = new apiBooksService($client);
		//Get volume service object
		$googleVols = $service->volumes;

		$optParams = array();
		$optParams['printType'] = "books";
		$q = "isbn:".$pISBN;

		//get volumes object
		$results = $googleVols->listVolumes($q, $optParams);

		//Get volume array
		$volumes = $results->getItems();
		
		if(count($volumes) == 0)
		{
			return 0;
		}

		if(count($volumes) > 1)
		{
			return 0;
		}

		$theBook = new Book();
		foreach($volumes as $volume)
		{
			//get volume id
			$theBook->setVolumeID($volume->getId());
			//get VolumeVolInfo object
			$volumeInfo = $volume->getVolumeInfo();
			//need author publisher, isbn
			$theBook->setName($volumeInfo->getTitle());
			$theBook->setNumAvailable(1);
			$theBook->setPCount($volumeInfo->getPageCount());
			$theBook->setDescription($volumeInfo->getDescription());
			$theBook->setQuantity(1);
			//get ISBN info
			//Get volumeIndustryInfo objec array
			$volumeIndInfo = $volumeInfo->getIndustryIdentifiers();
			$isbn10 = -1;
			$isbn13 = -1;
			//loop through array, get isbns
			foreach($volumeIndInfo as $indInfo)
			{

				if($indInfo->getType() == "ISBN_13")
				{
					$isbn13 = $indInfo->getIdentifier();
				}
				if($indInfo->getType() == "ISBN_10")
				{
					$isbn10 = $indInfo->getIdentifier();
				}
			}

			//use isbn13 by default, 10 as a fallback
			if($isbn13 != -1)
			{
				$theBook->setISBN($isbn13);
			}
			else
			{
				$theBook->setISBN($isbn10);
			}

			//get book authors and add to book object
			$authors = new Author();
			foreach($volumeInfo->getAuthors() as $author)
			{
				$authors->addAuthor($author);
			}
			$theBook->setAuthor($authors);

			//get publisher info
			$publishInfo = new Publisher();
			$publishInfo->setPublishDate($volumeInfo->getPublishedDate());
			$publishInfo->addPublisher($volumeInfo->getPublisher());
			$theBook->setPublisher($publishInfo);

		}	
		//Update access token
		if($client->getAccessToken())
		{
			$_SESSION['gb_api_token'] = $client->getAccessToken();
		}

		return $theBook;

	}

	public function find($pISBN, $pAuthor, $pTitle, $pKeywords)
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
		$client->setAssertionCredentials(new apiAssertionCredentials(GB_API_SERVICE_ACCOUNT_EMAIL, array('https://www.googleapis.com/auth/books'), $gb_key));
		$client->setClientId(GB_API_CLIENT_ID);

		$service = new apiBooksService($client);

		//Get BookshelvesVolumesServiceResource
		$bookshelf = $service->mylibrary_bookshelves_volumes;

		$optParams = array();
		$optParams['shelf'] = GB_API_BOOKSHELF_UID;
		$count = 0;
		$q = '';
		$authorQ = '';
		$titleQ = '';
		$keywordQ = '';	
		if($pAuthor != "")
		{
			foreach(preg_split("/ /", $pAuthor, -1, PREG_SPLIT_NO_EMPTY) as $author)	
			{
				if($count == 0)
				{
					$authorQ .= 'inauthor:'.$author.'';
				}
				else
				{
					$authorQ .= '+inauthor:'.$author.'';
				}
				$count ++;
			}
		}
		$count = 0;
		if($pTitle != "")
		{
			foreach(preg_split("/ /", $pTitle, -1, PREG_SPLIT_NO_EMPTY) as $title)
			{
				if($count == 0)
				{
					$titleQ .= 'intitle:'.$title.'';
				}
				else
				{
					$titleQ .= '+intitle:'.$title.'';
				}
				$count ++;
			}	
		}

		$count = 0;
		if($pKeywords != "")
		{
			foreach(preg_split("/ /", $pKeywords, -1, PREG_SPLIT_NO_EMPTY) as $keyword)
			{
				if($count == 0)
				{
					$keywordQ .= $keyword;
				}
				else
				{
					$keywordQ .= '+'.$keyword;
				}
				$count ++;
			}
		}
		//printf("Author String> %s\n", $authorQ);
		//printf("Title String> %s\n", $titleQ);
		//printf("Keywork String> %s\n", $keywordQ);
		
		if($keywordQ != '')	
		{
			$q .= $keywordQ;
		}
		if($titleQ != '')
		{
			if($q == '')
			{
				$q .= $titleQ;
			}
			else
			{
				$q .= '+'.$titleQ;
			}
		}
		if($authorQ != '')
		{
			if($q == '')
			{
				$q .= $authorQ;
			}
			else
			{
				$q .= '+'.$authorQ;
			}
		}
		//printf("Q String> %s\n", $q);
		
		$optParams['q'] = $q;
		//Get Volumes Ojbect
		$returnedVolumes = $bookshelf->listMylibraryBookshelvesVolumes($optParams);
		//Get Volume Object Array
		$volumes = $returnedVolumes->getItems();
		
		//printf("Total Results: %d", count($volumes));
		
		//return 0 on a search with no results	
		if(count($volumes) <= 0)
		{
			return 0;
		}


                $isbns = array();
                
		$theBooks = array();
		$count = 0;
		foreach($volumes as $volume)
		{
//			//get VolumeVolInfo object
			$volumeInfo = $volume->getVolumeInfo();
//			//Get volumeIndustryInfo objec array
			$volumeIndInfo = $volumeInfo->getIndustryIdentifiers();
			$isbn10 = -1;
			$isbn13 = -1;
			//loop through array, get isbns
			foreach($volumeIndInfo as $indInfo)
			{

				if($indInfo->getType() == "ISBN_13")
				{
					$isbn13 = $indInfo->getIdentifier();
				}
				if($indInfo->getType() == "ISBN_10")
				{
					$isbn10 = $indInfo->getIdentifier();
				}
			}

			//use isbn13 by default, 10 as a fallback
			if($isbn13 != -1)
			{
                            array_push($isbns, $isbn13);
			}
			else
			{
                            array_push($isbns, $isbn10);
			}
		}	
			
		//Update access token
		if($client->getAccessToken())
		{
			$_SESSION['gb_api_token'] = $client->getAccessToken();
		}
		
                return $isbns;
		//return $theBooks;
	}

	public function remove($pBookObject)
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
		$client->setAssertionCredentials(new apiAssertionCredentials(GB_API_SERVICE_ACCOUNT_EMAIL, array('https://www.googleapis.com/auth/books'), $gb_key));
		$client->setClientId(GB_API_CLIENT_ID);

		$service = new apiBooksService($client);
		$mylib = $service->mylibrary_bookshelves;
		//Book code goes here
		//	$shelf2 = $mylib->get(GB_API_BOOKSHELF_UID, array());
		//	echo $shelf2->getVolumeCount();
		#echo $mylib->addVolume(2, 'HFjLm2BauZ8C', array());
		#if(!$mylib->addVolume(GB_API_BOOKSHELF_UID, $pBookObject->getVolumeID, array()))
		$mylib->removeVolume(GB_API_BOOKSHELF_UID, $pBookObject->getVolumeID(), array());

		//Update access token
		if($client->getAccessToken())
		{
			$_SESSION['gb_api_token'] = $client->getAccessToken();
		}

		return 1;
	}
}
//$testBook = new Book();
//$gbObj = new GoogleBooks();
//$gbObj->remove($testBook);
//echo $gbObj->search(0000000000000);
//$books = $gbObj->find(0, "Michael", "Ruby", "computer");
//if($books == 0)
//{
//	printf("No Books\n");
//	exit;
//}
//foreach($books as $book)
//{
//	printf("\n");
//	printf($book);
//	printf("\n");
//}
?>
