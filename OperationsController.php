<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OperationsController
 *
 * @author Brad
 */
require_once 'BookControl.php';
require_once 'LDAPSearcher.php';
require_once 'UserActions.php';
require_once 'User.php';

class OperationsController {
    //put your code here
    public function renew($isbn, $user, $admin, $pass){
        $ldap = new LDAPSearcher();
        $_admin = $this->login($admin, $pass);
        if (empty($_admin)){
            return 0;
        }
        
        $_user = $ldap->getUser($user);
        if (empty($_user)){
            return 0;
        }
        
        $book_control = new BookControl();
        $book = $book_control->searchLibrary($isbn, null, null, null);
        if (empty($book)){
            return 0;
        }
        
        $user_actions = new UserActions();
        if ($user_actions->returnBook($book[0], $_user, $_admin) == 0){
            return 0;
        }
        $user_actions->checkoutBook($book[0], $_user, $_admin);
        return 1;
    }
    
    public function checkoutBook($isbn, $user, $admin, $pass){
        $ldap = new LDAPSearcher();
        $_admin = $this->login($admin, $pass);
        if (empty($_admin)){
            return 0;
        }
       
        
        $_user = $ldap->getUser($user);
        if (empty($_user)){
            return 0;
        }
        
        
        $book_control = new BookControl();
        $book = $book_control->searchLibrary($isbn, null, null, null);
        if (empty($book)){
            return 0;
        }
        
        
        $user_actions = new UserActions();
        if ($user_actions->checkoutBook($book[0], $_user, $_admin) == 0){
            return 0;
        }
        
        return 1;
    }
    
    public function returnBook($isbn, $user, $admin, $pass){
        $ldap = new LDAPSearcher();
        $_admin = $this->login($admin, $pass);
        if (empty($_admin)){
            return 0;
        }
        
        $_user = $ldap->getUser($user);
        if (empty($_user)){
            return 0;
        }
        
        $book_control = new BookControl();
        $book = $book_control->searchLibrary($isbn, null, null, null);
        if (empty($book)){
            return 0;
        }
        
        $user_actions = new UserActions();
        if ($user_actions->returnBook($book[0], $_user, $_admin) == 0){
            return 0;
        }
        return 1;
    }
    
    public function sendReminder(){
        $user_actions = new UserActions();
        $users_books = $user_actions->getCheckedOutUsers();
        foreach($users_books as $user_book){
            $ldap = new LDAPSearcher();
            $user = $ldap->getUser($user_book[0]);
            $book = $user_book[1];
            $to = $user->getEmail();
            $sender = 'bookreminder@acm.cs.uic.edu';
            $subject = "Book Reminder";
            $message = "This is an email to remind you that you have \"$book\" checked out.";
            $headers = "From: $sender\r\n" .
                    "X-Mailer: php";
            if (!(mail($to, $subject, $message, $headers))){
                return 0;
            }
        }
        return 1;
    }
    
    public function getCheckedOut(){
        $user_actions = new UserActions();
        return $user_actions->getCheckedOut();
    }
    
    public function addBook($isbn, $admin, $pass){
        $_admin = $this->login($admin, $pass);
        if (empty($_admin) || $_admin == -1 || $admin == 0){
            return 0;
        }
        $book_control = new BookControl();
        if ($book_control->addBook($isbn) ==0){
            return 0;
        }
        return 1;
    }
    
    public function removeBook($isbn, $admin, $pass){
        $_admin = $this->login($admin, $pass);
        if (empty($_admin) || $_admin == -1 || $admin == 0){
            return 0;
        }
        $book_control = new BookControl();
        if ($book_control->removeBook($isbn) == 0){
            return 0;
        }
        return 1;
    }
    
    public function updateBook($book_info, $admin, $pass){
        $_admin = $this->login($admin, $pass);
        if (empty($_admin) || $_admin == -1 || $admin == 0){
            return 0;
        }
        $book_control = new BookControl();
        if ($book_control->updateBook($book_info) == 0){
            return 0;
        }
        return 1;
    }
    
    public function search($isbn, $author, $title, $keywords){
        $book_control = new BookControl();
        return $book_control->searchLibrary($isbn, $author, $title, $keywords);
    }
    
    public function login($username, $pass){
	$ldap = new LDAPSearcher();
	
	$return = $ldap->isAdmin($username, $pass);
        if ($return == 0){
            return 0;
	}
	if ($return == -1)
	{
		return -1;
	}
        $user = new User();
        $user->setUserName($username);
        $user->setUserType(1);
        return $user;
    }
}

?>
