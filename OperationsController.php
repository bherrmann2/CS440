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
        $_admin;
        $_user;
        $ldap = new LDAPSearcher();
        $_admin = $ldap->login($admin, $pass);
        if ($_admin == -1){
            return 0;
        }
        
        $_user = $ldap->getUser($user);
        if ($_user == 0){
            return 0;
        }
        
        $book_control = new BookControl();
        $book;
        $book = $book_control->searchLibrary($isbn, null, null, null);
        if ($book_control == 0){
            return 0;
        }
        
        $user_actions = new UserActions();
        if ($user_actions->returnBook($book, $_user, $_admin) == 0){
            return 0;
        }
        $user_actions->checkoutBook($book, $_user, $_admin);
        return 1;
    }
    
    public function checkoutBook($isbn, $user, $admin, $pass){
        $_admin;
        $_user;
        $ldap = new LDAPSearcher();
        $_admin = $ldap->login($admin, $pass);
        if ($_admin == -1){
            return 0;
        }
        
        $_user = $ldap->getUser($user);
        if ($_user == 0){
            return 0;
        }
        
        $book_control = new BookControl();
        $book;
        $book = $book_control->searchLibrary($isbn, null, null, null);
        if ($book_control == 0){
            return 0;
        }
        
        $user_actions = new UserActions();
        if ($user_actions->checkoutBook($book, $_user, $_admin) == 0){
            return 0;
        }
        return 1;
    }
    
    public function returnBook($isbn, $user, $admin, $pass){
        $_admin;
        $_user;
        $ldap = new LDAPSearcher();
        $_admin = $ldap->login($admin, $pass);
        if ($_admin == -1){
            return 0;
        }
        
        $_user = $ldap->getUser($user);
        if ($_user == 0){
            return 0;
        }
        
        $book_control = new BookControl();
        $book;
        $book = $book_control->searchLibrary($isbn, null, null, null);
        if ($book_control == 0){
            return 0;
        }
        
        $user_actions = new UserActions();
        if ($user_actions->returnBook($book, $_user, $_admin) == 0){
            return 0;
        }
        return 1;
    }
    
    public function sendReminder(){
        $user_actions = new UserActions();
        $users;
        $users = $user_actions->getCheckoutOutUsers();
        foreach($user as $users){
            $to = $user->getUserName();
            $subject = "Book Reminder";
            $message = "This is an email to remind you that you have a book checked out.";
            $headers = 'From: bookreminder@acm.cs.uic.edu\r\nX-Mailer: php';
            if (mail($to, $subject, $message, $headers)){
                return 1;
            }else{
                return 0;
            }
        }
    }
    
    public function addBook($isbn, $admin, $pass){
        $_admin;
        $ldap = new LDAPSearcher();
        $_admin = $ldap->login($admin, $pass);
        if ($_admin == -1){
            return 0;
        }
        $book_control = new BookControl();
        $book_control->addBook($isbn);
        return 1;
    }
    
    public function removeBook($isbn, $admin, $pass){
        $_admin;
        $ldap = new LDAPSearcher();
        $_admin = $ldap->login($admin, $pass);
        if ($_admin == -1){
            return 0;
        }
        $book_control = new BookControl();
        if ($book_control->removeBook($isbn) == 0){
            return 0;
        }
        return 1;
    }
    
    public function updateBook($book_info, $admin, $pass){
        $_admin;
        $ldap = new LDAPSearcher();
        $_admin = $ldap->login($admin, $pass);
        if ($_admin == -1){
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
        if ($ldap->isAdmin($username, $pass) == 0){
            return 0;
        }
        $user = new User();
        $user->setUserType(1);
        return $user;
    }
}

?>
