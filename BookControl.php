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
            $isbn_array;
            $google_books = new GoogleBooks();
            $isbn_array = $google_books->find($isbn, $author, $title, $keywords);
        }else{
            $isbn_array = $isbn;
        }
        foreach ($isbn_array as $isbn){
            $sql_books = new MySQLBooks();
            array_push($book_array, $sql_books->search($isbn));
        }
        if (empty($book_array)){
            return 0;
        }else{
            return $book_array;
        }
    }
    
    public function addBook($isbn){
        $sql_books = new MySQLBooks();
        $book;
        $book = $sql_books->search($isbn);
        if ($book ==0){
            $google_books = new GoogleBooks();
            $google_books->add($isbn);
            $book = $google_books->search($isbn);
        }
        $sql_books->addBook($book);
        return 1;
    }
    
    public function updateBook($book_info){
        $book;
        $sql_books = new MySQLBooks();
        $book = $sql_books->search($book_info['isbn']);
        if ($book==0){
            return 0;
        }else{
            //set all of the vals in book to the data in book_info
            $book->setName($book_info['name']);
            $book->setISBN($book_info['isbn']);
            $book->setPCount($book_info['pcount']);
            $book->setDescription($book_info['desc']);
            $book->setQuantity($book_info['quantity']);
            $book->getPublisher()->setPublishDate($book_info['pdate']);
            //how are we handling multiple authors and publishers?
            $sql_books->updateBook($book);
            return 1;
        }
    }
    
    public function removeBook($isbn){
        $sql_books = new MySQLBooks();
        $book;
        $book = $sql_books->search($isbn);
        if ($book == 0){
            return 0;
        }else{
            $sql_books->removeBook($isbn);
            $google_books = new GoogleBooks();
            $google_books->remove($isbn);
            return 1;
        }
        
    }
}

?>
