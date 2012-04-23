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
        $query = sprintf("SELECT book_key, quantity, COUNT(*) FROM checked_out INNER JOIN books ON book=book_key WHERE isbn=%d AND returned=0 GROUP BY book",
            mysql_real_escape_string($book->getISBN()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        $row = mysql_fetch_row($result);
        if ($row[1] == $row[2]){ //all checked out
            return 0;
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
        $query = sprintf("SELECT book_key FROM checked_out INNER JOIN books ON book_key=book WHERE isbn=%d and returned=0",
            mysql_real_escape_string($book->getISBN()));
        $result = mysql_query($query);
        if (!$result){
            return 0;
        }
        
        if (mysql_num_rows($result) == 0){ //no book checked out
            return 0;
        }
        $row = mysql_fetch_array($result);
        
        $query = sprintf("UPDATE checked_out SET returned=1, r_admin='%s' WHERE book=%d",
            mysql_real_escape_string($admin->getUserName()),
            mysql_real_escape_string($row[0]));
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
}

?>
