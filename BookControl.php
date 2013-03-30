<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BookControl
 *
 * @author Brad
 */
require_once 'Book.php';
require_once 'GoogleBooks.php';
require_once 'MySQLBooks.php';

class BookControl {
    public function searchLibrary($isbn, $author, $title, $keywords){
        $book_array = array();
        if (empty($isbn)){
            $google_books = new GoogleBooks();
            if (empty($author) && empty($title) && empty($keywords)){
                return 0;
            }
            $isbn_array = $google_books->find($isbn, $author, $title, $keywords);
        }else{
            $isbn_array = array($isbn);
        }
        foreach ($isbn_array as $isbn){
            $sql_books = new MySQLBooks();
            $book = $sql_books->search($isbn);
            if (isset($book)){
            	array_push($book_array, $book);
	    }
        }
        if (empty($book_array)){
            return 0;
        }else{
            return $book_array;
        }
    }
    
    public function addBook($isbn){
        $sql_books = new MySQLBooks();
        $book = $sql_books->search($isbn);
        if (empty($book)){
            $google_books = new GoogleBooks();
            $book = $google_books->search($isbn);
            if (empty($book)){
                return 0;
            }
            $google_books->add($book);
        }
        $sql_books->addBook($book);
        return 1;
    }
    
    public function updateBook($book_info){
        $sql_books = new MySQLBooks();
        $book = $sql_books->search($book_info['isbn']);
        if (empty($book)){
            return 0;
        }else{
            $authors = $book->getAuthor()->getAuthor();
            foreach($authors as $author){
                $book->getAuthor()->removeAuthor($author);
            }
            $pubs = $book->getPublisher()->getPublisher();
            foreach($pubs as $pub){
                $book->getPublisher()->removePublisher($pub);
            }
            //set all of the vals in book to the data in book_info
            $book->setName($book_info['name']);
            $book->setISBN($book_info['isbn']);
            $book->setPCount($book_info['pcount']);
            $book->setDescription($book_info['desc']);
            $book->getPublisher()->setPublishDate($book_info['pdate']);
            for ($i=0;$i<3;$i++){
                if ($book_info["author$i"] != ""){
                    $book->getAuthor()->addAuthor($book_info["author$i"]);
                }
                if ($book_info["publisher$i"] != ""){
                    $book->getPublisher()->addPublisher($book_info["publisher$i"]);
                }
            }
            $sql_books->updateBook($book);
            return 1;
        }
    }
    
    public function removeBook($isbn){
        $sql_books = new MySQLBooks();
        $book = $sql_books->search($isbn);
        if (empty($book)){
            return 0;
        }else if ($book->getQuantity () > 1){
            return $sql_books->removeBook($isbn);
        }else{
            if ($sql_books->removeBook($isbn) == 0){
                return 0;
            }
            $google_books = new GoogleBooks();
            return $google_books->remove($book);
        }
        
    }
}

?>
