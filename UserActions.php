<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserActions
 *
 * @author Brad
 */
class UserActions {
    
    protected $link;
    
    public function __construct() {
        $this->link = mysql_connect('acm.cs.uic.edu', 'acmlib', 'AGDb4DrL8hYnatQ7');
        if (!$this->link){
            die('Could not connect');
        }
        mysql_select_db('library');
    }
    
    public function checkoutBook($book, $user, $admin){
        $query = sprintf("SELECT book_key, quantity FROM books WHERE isbn=%d",
            mysql_real_escape_string($book->getISBN()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        $row = mysql_fetch_row($result);
        
        $query = sprintf("SELECT COUNT(*) FROM checked_out WHERE book=%d AND returned=0 GROUP BY book",
            mysql_real_escape_string($row[0]));
        $result2 = mysql_query($query);
        if (!$result2){
            return 0;
        }
        if (mysql_num_rows($result2) != 0){
            $row2 = mysql_fetch_row($result2);
            if ($row[1] == $row2[0]){ //all checked out
                return 0;
            }
        }
        
        $query = sprintf("INSERT INTO checked_out (username, book, c_admin) VALUES('%s', %d, '%s')",
            mysql_real_escape_string($user->getUserName()),
            mysql_real_escape_string($row[0]),
            mysql_real_escape_string($admin->getUserName()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        return 1;
    }
    
    public function returnBook($book, $user, $admin){
        $query = sprintf("SELECT book_key FROM checked_out INNER JOIN books ON book_key=book WHERE isbn=%d and returned=0 AND username='%s'",
            mysql_real_escape_string($book->getISBN()),
            mysql_real_escape_string($user->getUserName()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        if (mysql_num_rows($result) == 0){ //no book checked out
            return 0;
        }
        $row = mysql_fetch_array($result);
        
        $query = sprintf("UPDATE checked_out SET returned=1, r_admin='%s' WHERE book=%d AND returned=0 AND username='%s'",
            mysql_real_escape_string($admin->getUserName()),
            mysql_real_escape_string($row[0]),
            mysql_real_escape_string($user->getUserName()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        return 1;
    }
    
    public function getCheckedOutUsers(){
        $query = "SELECT username FROM checked_out WHERE returned=0";
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        $users = array();
        
        while ($row = mysql_fetch_array($result)){
            $user = new User();
            $user->setUserName($row[0]);
            $user->setUserType(0);
            array_push($users, $user);
        }
        if (count($users)){
            return $users;
        }else{
            return 0;
        }
    }
    
    public function getCheckedOut(){
        $query = "SELECT username, name FROM checked_out INNER JOIN books ON book=book_key WHERE returned=0";
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        $user_book = array();
        
        while ($row = mysql_fetch_array($result)){
            array_push($user_book, $row[0]);
            array_push($user_book, $row[1]);
        }
        if (count($user_book)){
            return $user_book;
        }else{
            return 0;
        }
    }
}

?>
