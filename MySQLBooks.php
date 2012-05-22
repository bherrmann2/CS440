<?php

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/

/**
* Description of MySQLBooks
*
* @author Brad
*/
require_once 'Book.php';
require_once 'MySQLBooksConf.php';

class MySQLBooks {
    protected $link;
    
    public function __construct() {
        $this->link = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PW);
        if (!$this->link){
            die('Could not connect');
        }
        mysql_select_db(MYSQL_DB);
    }
    
    public function addBook($book){
        $query = sprintf("SELECT * FROM books WHERE isbn='%d'",
            mysql_real_escape_string($book->getISBN()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        if (mysql_num_rows($result) > 0){
            $query = sprintf("UPDATE books SET quantity=quantity+1 WHERE isbn='%d'",
                mysql_real_escape_string($book->getISBN()));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
        }else{
            $query = sprintf("INSERT INTO books (name, isbn, volume_id, page_count, quantity, publish_date, description) VALUES('%s', %d, '%s', %d, 1, '%s', '%s')",
                mysql_real_escape_string($book->getName()),
                mysql_real_escape_string($book->getISBN()),
                mysql_real_escape_string($book->getVolumeID()),
                mysql_real_escape_string($book->getPCount()),
                mysql_real_escape_string($book->getPublisher()->getPublishDate()),
                mysql_real_escape_string($book->getDescription()));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
            $query = sprintf("SELECT book_key FROM books WHERE isbn='%d'",
                mysql_real_escape_string($book->getISBN()));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
            $row = mysql_fetch_array($result);
            $book_key = $row[0];
            
            foreach ($book->getAuthor()->getAuthor() as $author){
                if ($this->authorLink($author, $book_key) == 0){
                    return 0;
                }
            }
            
            foreach($book->getPublisher()->getPublisher() as $publisher){
                if ($this->publisherLink($publisher, $book_key) == 0){
                    return 0;
                }
            }
        }
        return 1;
    }
    
    public function removeBook($isbn){
        $query = sprintf("SELECT * FROM books WHERE isbn='%d'",
            mysql_real_escape_string($isbn));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        if (mysql_num_rows($result) == 0){ //no book
            return 0;
        }
        $row = mysql_fetch_assoc($result);
        if ($row['quantity'] > 1){ //more than 1
            $query = sprintf("UPDATE books SET quantity=quantity-1 WHERE isbn='%d'",
                mysql_real_escape_string($isbn));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
        }else{ //one book
            $book_key = $row['book_key'];
            
            $query = sprintf("DELETE FROM books_authors WHERE book=%d",
                mysql_real_escape_string($book_key));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
            
            $query = sprintf("DELETE FROM books_publishers WHERE book=%d",
                mysql_real_escape_string($book_key));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
            
            $query = sprintf("DELETE FROM books WHERE book_key=%d",
                mysql_real_escape_string($book_key));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
        }
        return 1;
    }
    
    public function updateBook($book){
        $query = sprintf("SELECT * FROM books WHERE isbn='%d'",
            mysql_real_escape_string($book->getISBN()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        if (mysql_num_rows($result) == 0){ //no book
            return 0;
        }
        
        $row = mysql_fetch_array($result);
        $book_key = $row[0];
        
        $query = sprintf("UPDATE books SET name='%s', isbn=%d, volume_id='%s', page_count=%d, quantity=%d, publish_date='%s', description='%s' WHERE book_key=%d",
            mysql_real_escape_string($book->getName()),
            mysql_real_escape_string($book->getISBN()),
            mysql_real_escape_string($book->getVolumeID()),
            mysql_real_escape_string($book->getPCount()),
            mysql_real_escape_string($book->getQuantity()),
            mysql_real_escape_string($book->getPublisher()->getPublishDate()),
            mysql_real_escape_string($book->getDescription()),
            mysql_real_escape_string($book_key));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        $query = sprintf("DELETE FROM books_authors WHERE book=%d", //remove all links between the book and authors, so we can repopulate
            mysql_real_escape_string($book_key));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        $authors = $book->getAuthor()->getAuthor();
        
        foreach($authors as $author){
            if ($this->authorLink($author, $book_key) == 0){
                return 0;
            }
        }
        
        $query = sprintf("DELETE FROM books_publishers WHERE book=%d", //remove all links between the book and publishers, so we can repopulate
            mysql_real_escape_string($book_key));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        $publishers = $book->getPublisher()->getPublisher();
        foreach($publishers as $publisher){
            if ($this->publisherLink($publisher, $book_key) == 0){
                return 0;
            }
        }
        
        return 1;
    }
    
    public function search($isbn){
        $query = sprintf("SELECT * FROM books WHERE isbn='%d'",
            mysql_real_escape_string($isbn));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        if (mysql_num_rows($result) == 0){ //no book
            return 0;
        }
        
        $row = mysql_fetch_assoc($result);
        
        $book = new Book();
        $book->setDescription($row['description']);
        $book->setISBN($row['isbn']);
        $book->setVolumeID($row['volume_id']);
        $book->setName($row['name']);
        $book->setPCount($row['page_count']);
        $book->setQuantity($row['quantity']);
        $book->getPublisher()->setPublishDate($row['publish_date']);
        
        
        $query = sprintf("SELECT book_key, quantity FROM books WHERE isbn=%d",
            mysql_real_escape_string($book->getISBN()));
        $result2 = mysql_query($query);
        if (!$result2){
            return 0;
        }
        $row2 = mysql_fetch_row($result2);
        
        $query = sprintf("SELECT COUNT(*) FROM checked_out WHERE book=%d AND returned=0 GROUP BY book",
            mysql_real_escape_string($row2[0]));
        $result3 = mysql_query($query);
        if (!$result3){
            return 0;
        }
        if (mysql_num_rows($result3) != 0){
            $row3 = mysql_fetch_array($result3);
            $avail = $row2[1] - $row3[0];
        }else{
            $avail = $row2[1];
        }
        $book->setNumAvailable($avail);
        
        $query = sprintf("SELECT name FROM authors INNER JOIN books_authors ON author_key=author WHERE book=%d",
            mysql_real_escape_string($row['book_key']));
        $result2 = mysql_query($query);
        if (!$result2){
            return 0;
        }
        
        while ($row2 = mysql_fetch_array($result2)){
            $book->getAuthor()->addAuthor($row2[0]);
        }
        
        
        $query = sprintf("SELECT publishers.publisher FROM publishers INNER JOIN books_publishers ON publisher_key=books_publishers.publisher WHERE book=%d",
            mysql_real_escape_string($row['book_key']));
        $result2 = mysql_query($query);
        if (!$result2){
            return 0;
        }
        
        while ($row2 = mysql_fetch_array($result2)){
            $book->getPublisher()->addPublisher($row2[0]);
        }
        
        return $book;
        
        
        
    }
    
    protected function authorLink($author, $book_key){ //links a book to authors
        $query = sprintf("SELECT author_key FROM authors WHERE name='%s'",
            mysql_real_escape_string($author));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }

        if (mysql_num_rows($result) == 0){
            $query = sprintf("INSERT INTO authors (name) VALUES('%s')",
                mysql_real_escape_string($author));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
            $query = sprintf("SELECT author_key FROM authors WHERE name='%s'",
            mysql_real_escape_string($author));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
        }

        $row = mysql_fetch_array($result);
        $author_key = $row[0];

        $query = sprintf("INSERT INTO books_authors (book, author) VALUES(%d, %d)",
            mysql_real_escape_string($book_key),
            mysql_real_escape_string($author_key));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        return 1;
    }
    
    protected function publisherLink($publisher, $book_key){ //links a book to publishers
        $query = sprintf("SELECT publisher_key FROM publishers WHERE publisher='%s'",
            mysql_real_escape_string($publisher));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }

        if (mysql_num_rows($result) == 0){
            $query = sprintf("INSERT INTO publishers (publisher) VALUES('%s')",
                mysql_real_escape_string($publisher));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
            $query = sprintf("SELECT publisher_key FROM publishers WHERE publisher='%s'",
            mysql_real_escape_string($publisher));
            $result = mysql_query($query);
            if (!$result){
                return 0;
            }
        }

        $row = mysql_fetch_array($result);
        $publisher_key = $row[0];

        $query = sprintf("INSERT INTO books_publishers (book, publisher) VALUES(%d, %d)",
            mysql_real_escape_string($book_key),
            mysql_real_escape_string($publisher_key));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        return 1;
    }
}

?>
