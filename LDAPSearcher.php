<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LDAPSearcher
 *
 * @author Brad
 */
require_once 'User.php';

class LDAPSearcher {
    //put your code here
    public function getUser($username){
        $user = new User();
        $user->setUserName($username);
        $user->setUserType(0);
    }
    
    public function isAdmin($username, $password){
        return 1;
    }
    
}

?>
